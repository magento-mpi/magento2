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
class Magento_Adminhtml_Block_System_Store_DeleteTest extends PHPUnit_Framework_TestCase
{
    public function testGetHeaderText()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        /** @var $block Magento_Adminhtml_Block_System_Store_Delete */
        $block = $layout->createBlock('Magento_Adminhtml_Block_System_Store_Delete', 'block');

        $dataObject = new Magento_Object;
        $form = $block->getChildBlock('form');
        $form->setDataObject($dataObject);

        $expectedValue = 'header_text_test';
        $this->assertNotContains($expectedValue, (string)$block->getHeaderText());

        $dataObject->setName($expectedValue);
        $this->assertContains($expectedValue, (string)$block->getHeaderText());
    }
}
