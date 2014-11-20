<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;

    public function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_customerSession = $this->_objectManager->get(
            'Magento\Customer\Model\Session'
        );
        $service = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\CustomerAccountService'
        );
        $customer = $service->authenticate('customer@example.com', 'password');
        $this->_customerSession->setCustomerDataAsLoggedIn($customer);
        $this->_customerViewHelper = $this->_objectManager->create('Magento\Customer\Helper\View');
    }

    /**
     * @magentoConfigFixture current_store catalog/productalert/allow_price 1
     *
     * @magentoDataFixture Magento/ProductAlert/_files/product_alert.php
     */
    public function testProcess()
    {
        $this->_objectManager->configure(
            [
                'Magento\ProductAlert\Model\Observer' => [
                    'arguments' => [
                        'transportBuilder' => [
                            'instance' => 'Magento\TestFramework\Mail\Template\TransportBuilderMock'
                        ]
                    ]
                ],
                'Magento\ProductAlert\Model\Email' => [
                    'arguments' => [
                        'transportBuilder' => [
                            'instance' => 'Magento\TestFramework\Mail\Template\TransportBuilderMock'
                        ]
                    ]
                ],
                'preferences' => [
                    'Magento\Framework\Mail\TransportInterface' => 'Magento\TestFramework\Mail\TransportInterfaceMock',
                    'Magento\TestFramework\Mail\Template\TransportBuilder' =>
                        'Magento\TestFramework\Mail\Template\TransportBuilderMock'
                ]
            ]
        );
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->loadArea(\Magento\Framework\App\Area::AREA_FRONTEND);
        $observer = $this->_objectManager->get('Magento\ProductAlert\Model\Observer');
        $observer->process();

        /** @var \Magento\TestFramework\Mail\Template\TransportBuilderMock $transportBuilder */
        $transportBuilder = $this->_objectManager->get('Magento\TestFramework\Mail\Template\TransportBuilderMock');

        $this->assertStringMatchesFormat(
            '%AHello %A'
            . $this->_customerViewHelper->getCustomerName($this->_customerSession->getCustomerDataObject()) . ',%A',
            $transportBuilder->getSentMessage()->getBodyHtml()->getContent()
        );
    }
}
