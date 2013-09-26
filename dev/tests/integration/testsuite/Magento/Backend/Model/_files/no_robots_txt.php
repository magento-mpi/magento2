<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$robotsTxtPath = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Dir')->getDir()
    . DS . 'robots.txt';
if (is_file($robotsTxtPath)) {
    @unlink($robotsTxtPath);
}
