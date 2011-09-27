<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */


spl_autoload_register('magentoAutoloadForUnitTests');

function magentoAutoloadForUnitTests($class)
{
    $file = str_replace('_', '/', $class) . '.php';
    require_once $file;
}
