<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Address;

/**
 * Tests Address Edit Block
 */
class EditTest extends \PHPUnit_Framework_TestCase
{
    /** @var Edit */
    protected $_block;

    /** @var  \Magento\Customer\Model\Session */
    protected $_customerSession;

    /** @var \Magento\Backend\Block\Template\Context */
    protected $_context;

    /** @var string */
    protected $_requestId;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->_customerSession = $objectManager->get('\Magento\Customer\Model\Session');

        $this->_customerSession->setCustomerId(1);

        $this->_context = $objectManager->get('Magento\Backend\Block\Template\Context');
        $this->_requestId = $this->_context->getRequest()->getParam('id');
        $this->_context->getRequest()->setParam('id', '1');

        $objectManager->get('Magento\App\State')->setAreaCode('frontend');

        /** @var $layout \Magento\Core\Model\Layout */
        $layout = $objectManager->get('Magento\View\LayoutInterface');

        $this->_block = $layout->createBlock(
            'Magento\Customer\Block\Address\Edit',
            '',
            array('customerSession' => $this->_customerSession)
        );
    }

    protected function tearDown()
    {
        $this->_customerSession->unsCustomerId();
        $this->_context->getRequest()->setParam('id', $this->_requestId);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetSaveUrl()
    {
        $this->assertEquals('http://localhost/index.php/customer/address/formPost/', $this->_block->getSaveUrl());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testGetRegionId()
    {
        $this->assertEquals(1, $this->_block->getRegionId());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testCanSetAsDefaultShipping()
    {
        $this->assertEquals(0, $this->_block->canSetAsDefaultShipping());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIsDefaultBilling()
    {
        $this->assertFalse($this->_block->isDefaultBilling());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testGetStreetLine()
    {
        $this->assertEquals('Green str, 67', $this->_block->getStreetLine(1));
        $this->assertEquals('', $this->_block->getStreetLine(2));
    }
}
 