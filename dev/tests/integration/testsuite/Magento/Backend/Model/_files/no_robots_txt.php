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

$robotsTxtPath = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Dir')->getDir()
    . DS . 'robots.txt';
if (is_file($robotsTxtPath)) {
    @unlink($robotsTxtPath);
}
