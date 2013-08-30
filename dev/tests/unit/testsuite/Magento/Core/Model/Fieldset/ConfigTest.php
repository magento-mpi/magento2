<?php
/**
 * Magento_Core_Model_Fieldset_Config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Fieldset_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Fieldset_Config_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_Fieldset_Config
     */
    protected $_model;

    public function setUp()
    {
        $this->_storageMock = $this->getMock(
            'Magento_Core_Model_Fieldset_Config_Data',
            array('get'),
            array(),
            '',
            false
        );

        $this->_model = new Magento_Core_Model_Fieldset_Config($this->_storageMock);
    }

    public function testGetFieldsets()
    {
        $expected = array(
            'sales_convert_quote_address' => array(
                'company' => array(
                    'to_order_address' => '*',
                    'to_customer_address' => '*'
                ),
                'street_full' => array(
                    'to_order_address' => 'street'
                ),
                'street' => array(
                    'to_customer_address' => '*'
                )
            )
        );
        $this->_storageMock->expects($this->once())->method('get')
            ->will($this->returnValue($expected));
        $result = $this->_model->getFieldsets('global');
        $this->assertEquals($expected, $result);
    }
}
