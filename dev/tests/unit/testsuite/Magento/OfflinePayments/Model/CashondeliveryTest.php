<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @subpackage  unit_tests
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

        $eventManager = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);
        $paymentDataMock = $this->getMock('Magento\Payment\Helper\Data', array(), array(), '', false);
        $adapterFactoryMock = $this->getMock('Magento\Logger\AdapterFactory', array('create'), array(), '', false);

        $scopeConfig = $this->getMock('Magento\App\Config\ScopeConfigInterface');
        $this->_object = $helper->getObject(
            'Magento\OfflinePayments\Model\Cashondelivery',
            array(
                'eventManager' => $eventManager,
                'paymentData' => $paymentDataMock,
                'scopeConfig' => $scopeConfig,
                'logAdapterFactory' => $adapterFactoryMock
            )
        );
    }

    public function testGetInfoBlockType()
    {
        $this->assertEquals('Magento\Payment\Block\Info\Instructions', $this->_object->getInfoBlockType());
    }
}
