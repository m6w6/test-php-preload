--TEST--
php preload
--INI--
opcache.enable_cli=1
opcache.preload=preload.php
--FILE--
<?php
echo "TEST\n";
require "./index.php";
echo "DONE\n";
?>
--EXPECTF--
TEST
%a
            [classes] => Array
                (
                    [0] => c
                    [1] => b
                    [2] => a
                )
%a
DONE
