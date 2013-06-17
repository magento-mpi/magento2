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
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_ContentTest extends PHPUnit_Framework_TestCase
{
    public function testGetUploader()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        /** @var $block Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_Content */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_Content', 'block');

        $this->assertInstanceOf('Mage_Adminhtml_Block_Media_Uploader', $block->getUploader());
    }
}
