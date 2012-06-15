<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for block Mage_ImportExport_Block_Adminhtml_Import_Edit_FormTest
 */
class Mage_ImportExport_Block_Adminhtml_Import_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test content of form after _prepareForm
     */
    public function testPrepareForm()
    {
        $formBlock = new Mage_ImportExport_Block_Adminhtml_Import_Edit_Form();
        $prepareForm = new ReflectionMethod(
            'Mage_ImportExport_Block_Adminhtml_Import_Edit_Form',
            '_prepareForm'
        );
        $prepareForm->setAccessible(true);
        $prepareForm->invoke($formBlock);

        // check form
        $form = $formBlock->getForm();
        $this->assertInstanceOf('Varien_Data_Form', $form, 'Incorrect import form class.');
        $this->assertTrue($form->getUseContainer(), 'Form should use container.');

        // check form fieldsets
        $formFieldsetIds = array(
            'base_fieldset',
            'import_format_version_fieldset',
            'customer_entity_fieldset',
            'upload_file_fieldset'
        );
        $formFieldsets = array();
        $formElements = $form->getElements();
        foreach ($formElements as $element) {
            /** @var $element Varien_Data_Form_Element_Abstract */
            if (in_array($element->getId(), $formFieldsetIds)) {
                $formFieldsets[] = $element;
            }
        }
        $this->assertCount(count($formFieldsetIds), $formFieldsets);
        foreach ($formFieldsets as $fieldset) {
            $this->assertInstanceOf('Varien_Data_Form_Element_Fieldset', $fieldset, 'Incorrect fieldset class.');
        }
    }
}
