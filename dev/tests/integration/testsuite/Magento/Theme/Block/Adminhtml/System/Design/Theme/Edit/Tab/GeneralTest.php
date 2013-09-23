<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_GeneralTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Core_Model_Layout */
    protected $_layout;

    /** @var Magento_Core_Model_Theme */
    protected $_theme;

    /** @var Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_General */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');
        $this->_theme = Mage::getModel('Magento_Core_Model_Theme');
        $this->_theme->setType(Magento_Core_Model_Theme::TYPE_VIRTUAL);
        $this->_block = $this->_layout
            ->createBlock('Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_General');
    }

    public function testToHtmlPreviewImageNote()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->register('current_theme', $this->_theme);
        $this->_block->setArea('adminhtml');

        $this->_block->toHtml();

        $noticeText = $this->_block->getForm()->getElement('preview_image')->getNote();
        $this->assertNotEmpty($noticeText);
    }
}
