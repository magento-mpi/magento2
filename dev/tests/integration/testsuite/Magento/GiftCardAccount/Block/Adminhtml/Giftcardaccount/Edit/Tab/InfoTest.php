<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit\Tab;

/**
 * Test class for \Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit\Tab\Info
 *
 * @magentoAppArea adminhtml
 */
class InfoTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit\Tab\Info */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $model = \Mage::getModel('Magento\GiftCardAccount\Model\Giftcardaccount');
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_giftcardaccount', $model);

        $layout = \Mage::getSingleton('Magento\Core\Model\Layout');
        $this->_block = $layout
            ->createBlock('Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit\Tab\Info');
    }

    protected function tearDown()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->unregister('current_giftcardaccount');
        parent::tearDown();
    }

    /**
     * Test Prepare Form in Single Store mode
     *
     * @magentoConfigFixture current_store general/single_store_mode/enabled 1
     */
    public function testPrepareFormSingleStore()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $this->_block->initForm();
        $form = $this->_block->getForm();
        $this->assertEquals('base_fieldset', $form->getElement('base_fieldset')->getId());
        $this->assertNull($form->getElement('website_id'));
        $note = $form->getElement('balance')->getNote();
        $note = strip_tags($note);
        $this->assertNotEmpty($note);
    }

    /**
     * Test Prepare Form in Multiple Store mode
     *
     * @magentoConfigFixture current_store general/single_store_mode/enabled 0
     */
    public function testPrepareFormMultipleStore()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $this->_block->initForm();
        $form = $this->_block->getForm();
        $this->assertEquals('base_fieldset', $form->getElement('base_fieldset')->getId());

        $element = $form->getElement('website_id');
        $this->assertNotNull($element);
        $this->assertInstanceOf('Magento\Data\Form\Element\Select', $element);
        $this->assertEquals('website_id', $element->getId());

        $note = $form->getElement('balance')->getNote();
        $note = strip_tags($note);
        $this->assertEmpty($note);
    }

    public function testGetCurrencyJson()
    {
        $currencies = $this->_block->getCurrencyJson();
        $currencies = json_decode($currencies, true);
        $this->assertCount(1, $currencies);
        $this->assertEquals('USD', $currencies[1]);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testInitForm()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Mage::getSingleton('Magento\Core\Model\Layout');
        $block = $layout->addBlock('Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit\Tab\Info');

        $element = $block->initForm()->getForm()->getElement('date_expires');
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
