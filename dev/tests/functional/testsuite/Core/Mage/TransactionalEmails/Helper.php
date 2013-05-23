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
class Core_Mage_TransactionalEmails_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * <p>Create new template</p>
     *
     * @param array $templateData
     */
    public function createEmailTemplate(array $templateData)
    {
        $this->clickButton('add_new_template');
        $this->fillEmailTemplateData($templateData);
        $this->saveForm('save_template');
    }

    /**
     * @param array $templateData
     */
    public function fillEmailTemplateData(array $templateData)
    {
        if (isset($templateData['template'])) {
            $this->fillDropdown('template', $templateData['template']);
            $this->clickButton('load_template', false);
            $this->pleaseWait();
            unset($templateData['template']);
        }
        if (isset($templateData['variable_data'])) {
            foreach ($templateData['variable_data'] as $variable) {
                $this->cmsPagesHelper()->insertVariable($variable);
            }
            unset($templateData['variable_data']);
        }
        if (!empty($templateData)) {
            $this->fillFieldset($templateData, 'template_information');
        }
    }

    /**
     * Open Email Template.
     *
     * @param array $searchData
     */
    public function openEmailTemplate(array $searchData)
    {
        //Search Email Template.
        $searchData = $this->_prepareDataForSearch($searchData);
        $templateLocator = $this->search($searchData, 'system_email_template_grid');
        $this->assertNotNull($templateLocator, 'Email Template is not found with data: ' . print_r($searchData, true));
        $templateRowElement = $this->getElement($templateLocator);
        $templateUrl = $templateRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Template');
        $cellElement = $this->getChildElement($templateRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($templateUrl));
        //Open Email Template.
        $this->url($templateUrl);
        $this->validatePage('edit_email_template');
    }

    /**
     * <p>Delete template</p>
     *
     * @param array $searchData
     */
    public function deleteEmailTemplate(array $searchData)
    {
        $this->openEmailTemplate($searchData);
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
    }

    /**
     * <p>Edit template</p>
     *
     * @param array $searchData
     * @param array $newTemplateData
     */
    public function editEmailTemplate(array $searchData, array $newTemplateData)
    {
        $this->openEmailTemplate($searchData);
        $this->fillEmailTemplateData($newTemplateData);
        $this->saveForm('save_template');
    }

    /*
     * Check control presence
     * @param $controlType
     * @param $controls
     */
    public function checkControlsPresence($controlType, $controls)
    {
        foreach ($controls as $control) {
            if (!$this->controlIsPresent($controlType, $control)) {
                $this->addVerificationMessage("Control $control of type $controlType is not present on the page");
            }
        }
    }
}