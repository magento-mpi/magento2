<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Model;

class EmailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ProductAlert\Model\Email
     */
    protected $_model;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerService;

    /**
     * @var \Magento\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\ProductAlert\Block\Email\Price
     */
    protected $_block;

    protected function setUp()
    {
        $this->_customerService = $this->getMock(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface', [], [], '', false
        );
        $this->_block = $this->getMock('Magento\ProductAlert\Block\Email\Price', [], [], '', false);
        $blockEmail = $this->getMock('Magento\ProductAlert\Block\Email\AbstractEmail', [], [], '', false);
        $this->_block->expects($this->any())->method('setStore')->will($this->returnValue($blockEmail));
        $productAlertData = $this->getMock('Magento\ProductAlert\Helper\Data', [], [], '', false);
        $productAlertData->expects($this->any())->method('createBlock')->will($this->returnValue($this->_block));
        $this->_transportBuilder = $this->getMock('Magento\Mail\Template\TransportBuilder', [], [], '', false);
        $this->_transportBuilder->expects($this->any())->method('setTemplateIdentifier')->will($this->returnSelf());
        $this->_transportBuilder->expects($this->any())->method('setTemplateOptions')->will($this->returnSelf());
        $this->_transportBuilder->expects($this->any())->method('setFrom')->will($this->returnSelf());
        $transport = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\ProductAlert\Model\MockedMailTransport'
        );
        $this->_transportBuilder->expects($this->any())->method('getTransport')->will($this->returnValue($transport));
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\ProductAlert\Model\Email', [
                'customerAccountService' => $this->_customerService,
                'productAlertData' => $productAlertData,
                'transportBuilder' => $this->_transportBuilder
            ]
        );
    }

    public function testSetCustomerId()
    {
        $this->_customerService->expects($this->once())->method('getCustomer')->with(123);
        $this->_model->setCustomerId(123);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testSend()
    {
        $customerService = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\CustomerAccountService'
        );
        $customer = $customerService->getCustomer(1);
        $this->_customerService->expects($this->any())->method('getCustomer')->with(1)->will(
            $this->returnValue($customer)
        );
        $website = $this->getMock('Magento\Core\Model\Website', [], [], '', false);
        $website->expects($this->any())->method('getDefaultGroup')->will(
            $this->returnValue(new \Magento\Object(['default_store' => 1]))
        );
        $website->expects($this->any())->method('getDefaultStore')->will(
            $this->returnValue( new \Magento\Object(['id' => 1]))
        );
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
        $product->load(1);
        $helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Helper\View');
        $this->_transportBuilder->expects($this->once())->method('setTemplateVars')->with(
            array('customerName' => $helper->getCustomerName($customer), 'alertGrid' => $this->_block->toHtml())
        )->will($this->returnSelf());
        $this->_transportBuilder->expects($this->once())->method('addTo')->with(
            $customer->getEmail(),
            $helper->getCustomerName($customer)
        )->will($this->returnSelf());
        $this->_model->addPriceProduct($product);
        $this->_model->setWebsite($website);
        $this->_model->setCustomerId(1);
        $this->_model->send();
    }
}

class MockedMailTransport implements \Magento\Mail\TransportInterface
{
    /**
     * Mock of send a mail using transport
     *
     * @return void
     */
    public function sendMessage()
    {
        return;
    }
}
