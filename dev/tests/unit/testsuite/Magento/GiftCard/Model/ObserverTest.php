<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCard\Model\Observer
     */
    protected $_model;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * Test that dependency injections passed to the constructor will not be duplicated in _data property
     */
    public function testConstructorValidArguments()
    {
        $this->_model = $this->_objectManager->getObject(
            'Magento\GiftCard\Model\Observer',
            array(
                'itemsFactory' => $this->getMock(
                    'Magento\Sales\Model\Resource\Order\Invoice\Item\CollectionFactory',
                    array(),
                    array(),
                    '',
                    false
                ),
                'invoiceFactory' => $this->getMock(
                    'Magento\Sales\Model\Order\InvoiceFactory',
                    array(),
                    array(),
                    '',
                    false
                ),
                'data' => array('custom_field' => 'custom_value')
            )
        );
        $this->assertEquals(array('custom_field' => 'custom_value'), $this->_model->getData());
    }
}
