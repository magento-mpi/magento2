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

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_ContentTest extends PHPUnit_Framework_TestCase
{
    public function testGetUploader()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getModel('Magento_Core_Model_Layout');
        /** @var $block Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_Content */
        $block = $layout->createBlock('Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_Content', 'block');

        $this->assertInstanceOf('Magento_Adminhtml_Block_Media_Uploader', $block->getUploader());
    }
}
