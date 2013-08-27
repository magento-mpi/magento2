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

$robotsTxtPath = Mage::getBaseDir() . DS . 'robots.txt';
if (is_file($robotsTxtPath)) {
    @unlink($robotsTxtPath);
}
