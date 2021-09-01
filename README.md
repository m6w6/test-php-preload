
The manual regarding preloading seems to be off regarding when to use `opcache_compile_file` vs `require`:

Quoting https://www.php.net/manual/en/opcache.preloading.php -- _emphasis mine:_
> opcache_compile_file() **can load files in any order**. That is, if a.php defines class A and b.php defines class B that extends A, then opcache_compile_file() can load those two files in any order. When using include, however, a.php must be included first.

> Which approach is better therefore depends on the desired behavior. With code that would otherwise **use an autoloader, opcache_compile_file() allows for greater flexibility**. With code that would otherwise be loaded manually, include will be more robust.

---

The result of the following simple test indicate the contrary, though.

Running
```shell
require=0 php -d opcache.enable_cli=1 -d opcache.preload=t/preload.php t/index.php >t/compile.txt
```

and
```shell
require=1 php -d opcache.enable_cli=1 -d opcache.preload=t/preload.php t/index.php >t/require.txt
```

results in the following (stripped down) differences:
```diff
--- t/compile.txt
+++ t/require.txt
@@ -1,13 +1,15 @@
-array(1) {
+autoload(b) -> require b.php
+autoload(c) -> require c.php
+array(4) {
   [0]=>
   string(45) "/Users/mike/Sources/php-preload/t/preload.php"
+  [1]=>
+  string(39) "/Users/mike/Sources/php-preload/t/a.php"
+  [2]=>
+  string(39) "/Users/mike/Sources/php-preload/t/b.php"
+  [3]=>
+  string(39) "/Users/mike/Sources/php-preload/t/c.php"
 }
-
-Warning: Can't preload class c with unresolved initializer for static property $c in /Users/mike/Sources/php-preload/t/c.php on line 3
-
-Warning: Can't preload unlinked class b: Parent with unresolved initializers c in /Users/mike/Sources/php-preload/t/b.php on line 3
-
-Warning: Can't preload unlinked class a: Unknown parent b in /Users/mike/Sources/php-preload/t/a.php on line 3
 Array
 (
     [opcache_enabled] => 1
@@ -49,13 +51,20 @@
 
     [preload_statistics] => Array
         (
             [functions] => Array
                 (
                     [0] => {closure}
                     [1] => {closure}
                 )
 
+            [classes] => Array
+                (
+                    [0] => c
+                    [1] => b
+                    [2] => a
+                )
+
             [scripts] => Array
                 (
                     [0] => /Users/mike/Sources/php-preload/t/preload.php
```

Judge for yourself, but it looks like using `require` plus `autoload` is more robust.  