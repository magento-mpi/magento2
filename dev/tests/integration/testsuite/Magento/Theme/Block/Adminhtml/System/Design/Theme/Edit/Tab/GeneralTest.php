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

namespace Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab;

/**
 * @magentoAppArea adminhtml
 */
class GeneralTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Core\Model\Layout */
    protected $_layout;

    /** @var \Magento\Core\Model\Theme */
    protected $_theme;

    /** @var \Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\General */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = \Mage::getSingleton('Magento\Core\Model\Layout');
        $this->_theme = \Mage::getModel('Magento\Core\Model\Theme');
        $this->_theme->setType(\Magento\Core\Model\Theme::TYPE_VIRTUAL);
        $this->_block = $this->_layout
            ->createBlock('Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\General');
    }

    public function testToHtmlPreviewImageNote()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_theme', $this->_theme);
        $this->_block->setArea('adminhtml');

        $this->_block->toHtml();

        $noticeText = $this->_block->getForm()->getElement('preview_image')->getNote();
        $this->assertNotEmpty($noticeText);
    }
}
