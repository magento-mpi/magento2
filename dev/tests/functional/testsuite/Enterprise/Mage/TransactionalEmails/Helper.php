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
 * @method Community2_Mage_TransactionalEmails_Helper helper(string $className)
 */
class Enterprise_Mage_TransactionalEmails_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Inserts variable
     *
     * @param string $variable
     */
    public function insertVariable($variable)
    {
        $this->helper('Community2/Mage/TransactionalEmails/Helper')->insertVariable($variable);
    }

    /** <p>Fill fields in Template form according to the resulting array</p>
     *
     * @param array $templateData
     * @param string
     */
    public function fillTemplateForm(array $templateData, $fieldName)
    {
        $this->helper('Community2/Mage/TransactionalEmails/Helper')->fillTemplateForm($templateData, $fieldName);
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
        $this->helper('Community2/Mage/TransactionalEmails/Helper')
            ->createNewTemplate($templateData, $templateName, $variable);
    }

    /**
     * <p>Delete template</p>
     *
     * @param array $searchData
     */
    public function deleteTemplate(array $searchData)
    {
        $this->helper('Community2/Mage/TransactionalEmails/Helper')->deleteTemplate($searchData);
    }

    /**
     * <p>Edit template</p>
     *
     * @param array $searchData
     * @param array $newTemplateData
     */
    public function editTemplate(array $searchData, array $newTemplateData)
    {
        $this->helper('Community2/Mage/TransactionalEmails/Helper')->editTemplate($searchData, $newTemplateData);
    }
}