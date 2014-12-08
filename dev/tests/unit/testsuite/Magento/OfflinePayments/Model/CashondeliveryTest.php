<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\OfflinePayments\Model;

class CashondeliveryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\OfflinePayments\Model\Cashondelivery
     */
    protected $_object;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);
        $paymentDataMock = $this->getMock('Magento\Payment\Helper\Data', [], [], '', false);
        $adapterFactoryMock = $this->getMock(
            'Magento\Framework\Logger\AdapterFactory',
            ['create'],
            [],
            '',
            false
        );

        $scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->_object = $helper->getObject(
            'Magento\OfflinePayments\Model\Cashondelivery',
            [
                'eventManager' => $eventManager,
                'paymentData' => $paymentDataMock,
                'scopeConfig' => $scopeConfig,
                'logAdapterFactory' => $adapterFactoryMock
            ]
        );
    }

    public function testGetInfoBlockType()
    {
        $this->assertEquals('Magento\Payment\Block\Info\Instructions', $this->_object->getInfoBlockType());
    }
}
