<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_TransactionalEmails
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_TransactionalEmails_Helper extends Mage_Selenium_TestCase
{
    /**
     * Inserts variable
     *
     * @param string $variable
     */
    public function insertVariable($variable)
    {
        if (!empty($variable)) {
            $this->clickButton('insert_variable', false);
            $this->waitForAjax();
            $this->clickControl('link', $variable, false);
            $this->waitForAjax();
        }
    }

    /** <p>Fill fields in Template form according to the resulting array</p>
     *
     * @param array $templateData
     * @param string
     */
    public function fillTemplateForm(array $templateData, $fieldName)
    {
        if (!empty($templateData)) {
            $this->fillFieldset($templateData, $fieldName);
        }
    }

    /**
     * <p>Create new template</p>
     *
     * @param array $templateData
     * @param array $templateName
     * @param string $variable
     */
    public function createNewTemplate(array $templateData, array $templateName, $variable)
    {
        $this->clickButton('add_new_template');
        if (!empty($templateData)) {
            $this->fillTemplateForm($templateData, 'load_default_template');
            $this->clickButton('load_template', false);
            $this->waitForAjax();
            if (!empty($variable)) {
                $this->insertVariable($variable);
            }
            if (!empty($templateName)) {
                $this->fillTemplateForm($templateName, 'template_information');
                $this->clickButton('save_template');
            }
        }
    }

    /**
     * <p>Delete template</p>
     *
     * @param array $searchData
     */
    public function deleteTemplate(array $searchData)
    {
        if (!isset($searchData['filter_template_name'])) {
            $this->fail('Required data for deleting are empty');
        }
        //Data
        $this->addParameter('template_name', $searchData['filter_template_name']);
        //Steps
        $this->searchAndOpen($searchData);
        //Verifying
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
    }

    /**
     * <p>Edit template</p>
     *
     * @param array $searchData
     * @param array $newTemplateData
     */
    public function editTemplate(array $searchData, array $newTemplateData)
    {
        if (empty($newTemplateData)) {
            $this->fail('$newTemplateData is empty');
        }
        if (!isset($searchData['filter_template_name'])) {
            $this->fail('filter_template_name is empty');
        }
        $this->addParameter('template_name', $searchData['filter_template_name']);
        //Steps
        $this->searchAndOpen($searchData);
        $this->fillTemplateForm($newTemplateData, 'template_information');
        $this->clickButton('save_template');
    }
}