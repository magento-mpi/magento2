<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Data
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Form\Element;

class MultiselectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Data\Form\Element\Multiselect
     */
    protected $_model;

    protected function setUp()
    {
        $coreHelper = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $factoryElement = $this->getMock('Magento\Data\Form\Element\Factory', array(), array(), '', false);
        $collectionFactory = $this->getMock('Magento\Data\Form\Element\CollectionFactory', array(), array(), '', false);
        $this->_model = new \Magento\Data\Form\Element\Multiselect($coreHelper, $factoryElement, $collectionFactory);
        $this->_model->setForm(new \Magento\Object());
    }

    /**
     * Verify that hidden input is present in multiselect
     *
     * @covers \Magento\Data\Form\Element\Multiselect::getElementHtml
     */
    public function testHiddenFieldPresentInMultiSelect()
    {
        $this->_model->setDisabled(true);
        $this->_model->setCanBeEmpty(true);
        $elementHtml = $this->_model->getElementHtml();
        $this->assertContains('<input type="hidden"', $elementHtml);
    }
}
