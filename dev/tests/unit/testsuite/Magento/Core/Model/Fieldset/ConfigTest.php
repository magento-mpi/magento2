<?php
/**
 * \Magento\Core\Model\Fieldset\Config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Fieldset_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Fieldset\Config\Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Fieldset\Config
     */
    protected $_model;

    public function setUp()
    {
        $this->_storageMock = $this->getMock(
            'Magento\Core\Model\Fieldset\Config\Data',
            array('get'),
            array(),
            '',
            false
        );

        $this->_model = new \Magento\Core\Model\Fieldset\Config($this->_storageMock);
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

    public function testGetFieldset()
    {
        $expectedFieldset = array(
            'aspect' => 'firstAspect'
        );
        $fieldsets = array(
            'test' => $expectedFieldset,
            'test_second' => array(
                'aspect' => 'secondAspect'
            ),
        );
        $this->_storageMock->expects($this->once())->method('get')
            ->will($this->returnValue($fieldsets));
        $result = $this->_model->getFieldset('test');
        $this->assertEquals($expectedFieldset, $result);
    }
}
