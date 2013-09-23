<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_FileTest extends PHPUnit_Framework_TestCase
{
    public function testGetHtmlAttributes()
    {
        /** @var $fileBlock Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_File */
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $collectionFactory = $this->getMock('Magento_Data_Form_Element_CollectionFactory', array(), array(), '', false);

        $fileBlock = $helper->getObject('Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_File',
            array('factoryCollection' => $collectionFactory)
        );

        $this->assertContains('accept', $fileBlock->getHtmlAttributes());
        $this->assertContains('multiple', $fileBlock->getHtmlAttributes());
    }
}
