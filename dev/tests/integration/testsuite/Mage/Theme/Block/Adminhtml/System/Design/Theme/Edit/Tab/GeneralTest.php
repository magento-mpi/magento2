<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_GeneralTest extends Mage_Backend_Area_TestCase
{
    /** @var Mage_Core_Model_Layout */
    protected $_layout;

    /** @var Mage_Core_Model_Theme */
    protected $_theme;

    /** @var Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_General */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = Mage::getModel('Mage_Core_Model_Layout');
        $this->_theme = Mage::getModel('Mage_Core_Model_Theme');
        $this->_block = $this->_layout->createBlock('Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_General');
    }

    public function testToHtmlPreviewImageNote()
    {
        Mage::register('current_theme', $this->_theme);
        $this->_block->setArea('adminhtml');

        $this->_block->toHtml();

        $noticeText = $this->_block->getForm()->getElement('preview_image')->getNote();
        $this->assertNotEmpty($noticeText);
    }
}
