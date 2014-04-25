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
namespace Magento\Framework\Data\Form\Element;

class EditablemultiselectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Editablemultiselect
     */
    protected $_model;

    protected function setUp()
    {
        $testHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $testHelper->getObject('Magento\Framework\Data\Form\Element\Editablemultiselect');
        $values = array(
            array('value' => 1, 'label' => 'Value1'),
            array('value' => 2, 'label' => 'Value2'),
            array('value' => 3, 'label' => 'Value3')
        );
        $value = array(1, 3);
        $this->_model->setForm(new \Magento\Framework\Object());
        $this->_model->setData(array('values' => $values, 'value' => $value));
    }

    public function testGetElementHtmlRendersDataAttributesWhenDisabled()
    {
        $this->_model->setDisabled(true);
        $elementHtml = $this->_model->getElementHtml();
        $this->assertContains('disabled="disabled"', $elementHtml);
        $this->assertContains('data-is-removable="no"', $elementHtml);
        $this->assertContains('data-is-editable="no"', $elementHtml);
    }

    public function testGetElementHtmlRendersRelatedJsClassInitialization()
    {
        $this->_model->setElementJsClass('CustomSelect');
        $elementHtml = $this->_model->getElementHtml();
        $this->assertContains('ElementControl = new CustomSelect(', $elementHtml);
        $this->assertContains('ElementControl.init();', $elementHtml);
    }
}
