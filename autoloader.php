<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


set_include_path(join(PATH_SEPARATOR, array(
    UNIT_ROOT . '/tests',
    UNIT_FRAMEWORK,
    UNIT_FRAMEWORK . '/_stubs',
    get_include_path()
)));


spl_autoload_register('mageAutoloader');

/**
 * Unit auto class loader
 *
 * @param string $class
 * @return void
 * @throws Magento_Exception
 */
function mageAutoloader($class)
{
    static $paths;
    if (null === $paths) {
        $paths = explode(PATH_SEPARATOR, get_include_path());
    }
    $file = str_replace('_', '/', $class) . '.php';

    foreach ($paths as $path) {
        $filename = $path . DIRECTORY_SEPARATOR . $file;
        if (file_exists($filename)) {
            require_once $filename;
            return;
        }
    }
    throw new Magento_Exception(
        sprintf('Class does not exist in path "%s"', get_include_path()));

}
