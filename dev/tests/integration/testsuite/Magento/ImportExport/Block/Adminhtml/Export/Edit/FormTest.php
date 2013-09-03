<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for block Magento_ImportExport_Block_Adminhtml_Export_Edit_Form
 * @magentoAppArea adminhtml
 */
class Magento_ImportExport_Block_Adminhtml_Export_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * Testing model
     *
     * @var Magento_ImportExport_Block_Adminhtml_Export_Edit_Form
     */
    protected $_model;

    /**
     * Expected form fieldsets and fields
     * array (
     *     <fieldset_id> => array(
     *         <element_id> => <element_name>,
     *         ...
     *     ),
     *     ...
     * )
     *
     * @var array
     */
    protected $_expectedFields = array(
        'base_fieldset' => array(
            'entity'      => 'entity',
            'file_format' => 'file_format',
        ),
    );

    public function setUp()
    {
        parent::setUp();
        $this->_model = Mage::app()->getLayout()->createBlock('Magento_ImportExport_Block_Adminhtml_Export_Edit_Form');
    }

    /**
     * Test preparing of form
     *
     * @covers Magento_ImportExport_Block_Adminhtml_Export_Edit_Form::_prepareForm
     */
    public function testPrepareForm()
    {
        // invoking _prepareForm
        $this->_model->toHtml();

        // get fieldset list
        $actualFieldsets = array();
        $formElements = $this->_model->getForm()
            ->getElements();
        foreach ($formElements as $formElement) {
            if ($formElement instanceof \Magento\Data\Form\Element\Fieldset) {
                $actualFieldsets[] = $formElement;
            }
        }

        // assert fieldsets and fields
        $this->assertSameSize($this->_expectedFields, $actualFieldsets);
        /** @var $actualFieldset \Magento\Data\Form\Element\Fieldset */
        foreach ($actualFieldsets as $actualFieldset) {
            $this->assertArrayHasKey($actualFieldset->getId(), $this->_expectedFields);
            $expectedFields = $this->_expectedFields[$actualFieldset->getId()];
            /** @var $actualField \Magento\Data\Form\Element\AbstractElement */
            foreach ($actualFieldset->getElements() as $actualField) {
                $this->assertArrayHasKey($actualField->getId(), $expectedFields);
                $this->assertEquals($expectedFields[$actualField->getId()], $actualField->getName());
            }
        }
    }
}
