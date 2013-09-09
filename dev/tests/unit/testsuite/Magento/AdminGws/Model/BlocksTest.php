<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_AdminGws_Model_BlocksTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_AdminGws_Model_Blocks
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Magento_AdminGws_Model_Blocks(
            $this->getMock('Magento_AdminGws_Model_Role', array(), array(), '', false)
        );
    }

    public function testDisableTaxRelatedMultiselects()
    {
        $form = $this->getMock('Magento_Data_Form', array('getElement' ,'setDisabled'), array(), '', false);
        $form->expects($this->exactly(3))
            ->method('getElement')
            ->with($this->logicalOr(
                $this->equalTo('tax_customer_class'),
                $this->equalTo('tax_product_class'),
                $this->equalTo('tax_rate'))
            )
            ->will($this->returnSelf());

        $form->expects($this->exactly(3))
            ->method('setDisabled')
            ->with($this->equalTo(true))
            ->will($this->returnSelf());

        $observerMock = new Magento_Object(array(
            'event' => new Magento_Object(array(
                'block' => new Magento_Object(array('form' => $form))
            ))
        ));

        $this->_model->disableTaxRelatedMultiselects($observerMock);
    }
}
