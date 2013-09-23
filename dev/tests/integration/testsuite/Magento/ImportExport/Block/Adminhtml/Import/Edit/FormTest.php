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
 * Tests for block Magento_ImportExport_Block_Adminhtml_Import_Edit_FormTest
 * @magentoAppArea adminhtml
 */
class Magento_ImportExport_Block_Adminhtml_Import_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * List of expected fieldsets in import edit form
     *
     * @var array
     */
    protected $_expectedFieldsets = array(
        'base_fieldset',
        'upload_file_fieldset',
    );

    /**
     * Add behaviour fieldsets to expected fieldsets
     *
     * @static
     */
    protected function setUp()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $importModel = $objectManager->create('Magento_ImportExport_Model_Import');

        $uniqueBehaviors = $importModel->getUniqueEntityBehaviors();
        foreach (array_keys($uniqueBehaviors) as $behavior) {
            $this->_expectedFieldsets[] = $behavior . '_fieldset';
        }
    }

    /**
     * Test content of form after _prepareForm
     */
    public function testPrepareForm()
    {
        $formBlock = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_ImportExport_Block_Adminhtml_Import_Edit_Form');
        $prepareForm = new ReflectionMethod(
            'Magento_ImportExport_Block_Adminhtml_Import_Edit_Form',
            '_prepareForm'
        );
        $prepareForm->setAccessible(true);
        $prepareForm->invoke($formBlock);

        // check form
        $form = $formBlock->getForm();
        $this->assertInstanceOf('Magento_Data_Form', $form, 'Incorrect import form class.');
        $this->assertTrue($form->getUseContainer(), 'Form should use container.');

        // check form fieldsets
        $formFieldsets = array();
        $formElements = $form->getElements();
        foreach ($formElements as $element) {
            /** @var $element Magento_Data_Form_Element_Abstract */
            if (in_array($element->getId(), $this->_expectedFieldsets)) {
                $formFieldsets[] = $element;
            }
        }
        $this->assertSameSize($this->_expectedFieldsets, $formFieldsets);
        foreach ($formFieldsets as $fieldset) {
            $this->assertInstanceOf('Magento_Data_Form_Element_Fieldset', $fieldset, 'Incorrect fieldset class.');
        }
    }
}
