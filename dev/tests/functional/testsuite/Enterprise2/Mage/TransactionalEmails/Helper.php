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
class Enterprise2_Mage_TransactionalEmails_Helper extends Mage_Selenium_TestCase
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

    /**
     * Create new template
     *
     * @param array $templateData
     * @param array $templateName
     * @param string $variable
     */
    public function createNewTemplate(array $templateData, array $templateName, $variable)
    {
        $this->clickButton('add_new_template');
        if (!empty($templateData)) {
            $this->fillFieldset($templateData, 'load_default_template');
            $this->clickButton('load_template', false);
            $this->waitForAjax();
            if (!empty($variable)) {
                $this->insertVariable($variable);
            }
            if (!empty($templateName)) {
                $this->fillFieldset($templateName, 'template_information');
                $this->clickButton('save_template');
            }
        }
    }
}