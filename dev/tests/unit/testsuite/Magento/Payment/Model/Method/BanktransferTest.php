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

namespace Magento\Payment\Model\Method;

class BanktransferTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Model\Method\Banktransfer
     */
    protected $_object;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $eventManager = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);
        $paymentDataMock = $this->getMock('Magento\Payment\Helper\Data', array(), array(), '', false);
        $coreStoreConfig = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false);
        $adapterFactoryMock = $this->getMock('Magento\Core\Model\Log\AdapterFactory', array('create'),
            array(), '', false);
        $this->_object = $objectManagerHelper->getObject('Magento\Payment\Model\Method\Banktransfer', array(
            'eventManager' => $eventManager,
            'paymentData' => $paymentDataMock,
            'coreStoreConfig' => $coreStoreConfig,
            'logAdapterFactory' => $adapterFactoryMock,
        ));
    }

    public function testGetInfoBlockType()
    {
        $this->assertEquals('Magento\Payment\Block\Info\Instructions', $this->_object->getInfoBlockType());
    }
}
