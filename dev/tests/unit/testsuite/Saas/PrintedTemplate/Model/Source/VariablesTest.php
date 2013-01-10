<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Model_Source_VariablesTest extends PHPUnit_Framework_TestCase
{

   /**
    * Emulate string translation
    *
    * @param mixed $text
    * @return string
    */
    public function translate($text)
    {
        return ($text . '_translate');
    }

   /**
    * test toOptionArray method
    *
    * @param array $variables
    * @params array $expected
    *
    * @dataProvider testToOptionArrayProvider
    */
    public function testToOptionArray($variables, $templateType, $expected)
    {
        $configMock = $this->getMockBuilder('Saas_PrintedTemplate_Model_Config')
            ->setMethods(array('getVariablesArray'))
            ->getMock();

        $configMock->expects($this->any())
            ->method('getVariablesArray')
            ->with($this->equalTo($templateType))
            ->will($this->returnValue($variables));

        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Source_Variables')
            ->setMethods(array('_getConfigModelSingeleton', '_getHelper'))
            ->getMock();

        $model->expects($this->once())
            ->method('_getConfigModelSingeleton')
            ->will($this->returnValue($configMock));

        $helperMock = $this->getMockBuilder('Saas_PrintedTemplate_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('__'))
            ->getMock();

        $helperMock->expects($this->any())
            ->method('__')
            ->will($this->returnCallback(array($this, 'translate')));

        $model->expects($this->any())
            ->method('_getHelper')
            ->will($this->returnValue($helperMock));

        $optionArray = $model->toOptionArray($templateType);
        $this->assertSame($expected, $optionArray);
    }

    /**
        * provide data for toOptionArrayProvider method
        *
        * @return array
        */
    public function testToOptionArrayProvider()
    {
        return array(
            array(
                array(array(
                    'label' => 'Customer',
                    'fields' => array(
                        'customer_balance_ammount' => array(
                            'label' => 'Balance Amount',
                            'type' => 'currency'
                        ),
                        'customer_balance_invoiced' => array(
                            'label' => 'Invoiced Balance Amount',
                            'type' => 'currency'
                        ),
                        'customer_balance_refunded' => array(
                            'label' => 'Refunded Balance Amount',
                            'type' => 'currency'
                        )
                    )
                )),
                'customer',
                array(array(
                    'label' => $this->translate('Customer'),
                    'value' => array(
                        array(
                            'value'=> '{{var 0.customer_balance_ammount}}',
                            'label' => $this->translate('Balance Amount')
                        ),
                        array(
                            'value'=> '{{var 0.customer_balance_invoiced}}',
                            'label' => $this->translate('Invoiced Balance Amount')
                        ),
                        array(
                            'value'=> '{{var 0.customer_balance_refunded}}',
                            'label' => $this->translate('Refunded Balance Amount')
                        ),
                    )
                ))
            ),
            array(array(), 'customer', array()),
        );
    }
}
