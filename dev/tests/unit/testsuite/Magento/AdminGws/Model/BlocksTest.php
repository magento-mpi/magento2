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
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject('Magento\AdminGws\Model\Blocks');
    }

    public function testDisableTaxRelatedMultiselects()
    {
        $form = $this->getMock('Magento\Framework\Data\Form', array('getElement', 'setDisabled'), array(), '', false);
        $form->expects(
            $this->exactly(3)
        )->method(
            'getElement'
        )->with(
            $this->logicalOr(
                $this->equalTo('tax_customer_class'),
                $this->equalTo('tax_product_class'),
                $this->equalTo('tax_rate')
            )
        )->will(
            $this->returnSelf()
        );

        $form->expects(
            $this->exactly(3)
        )->method(
            'setDisabled'
        )->with(
            $this->equalTo(true)
        )->will(
            $this->returnSelf()
        );

        $observerMock = new \Magento\Framework\Object(
            array('event' => new \Magento\Framework\Object(array('block' => new \Magento\Framework\Object(array('form' => $form)))))
        );

        $this->_model->disableTaxRelatedMultiselects($observerMock);
    }
}
