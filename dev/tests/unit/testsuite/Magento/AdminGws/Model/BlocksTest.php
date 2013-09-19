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

namespace Magento\AdminGws\Model;

class BlocksTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdminGws\Model\Blocks
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\AdminGws\Model\Blocks(
            $this->getMock('Magento\AdminGws\Model\Role', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false)
        );
    }

    public function testDisableTaxRelatedMultiselects()
    {
        $form = $this->getMock('Magento\Data\Form', array('getElement' ,'setDisabled'), array(), '', false);
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

        $observerMock = new \Magento\Object(array(
            'event' => new \Magento\Object(array(
                'block' => new \Magento\Object(array('form' => $form))
            ))
        ));

        $this->_model->disableTaxRelatedMultiselects($observerMock);
    }
}
