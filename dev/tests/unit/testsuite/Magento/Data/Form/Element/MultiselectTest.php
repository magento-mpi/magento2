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
        $testHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $testHelper->getObject('Magento\Data\Form\Element\Editablemultiselect');
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

    /**
     * Verify that js element is added
     */
    public function testGetAfterElementJs()
    {
        $this->_model->setAfterElementJs('<script language="text/javascript">var website = "website1";</script>');
        $elementHtml = $this->_model->getAfterElementJs();
        $this->assertContains('var website = "website1";', $elementHtml);
    }
}
