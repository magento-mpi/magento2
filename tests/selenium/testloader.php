<?php
function __autoload($class) {
    include str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';
}
?>