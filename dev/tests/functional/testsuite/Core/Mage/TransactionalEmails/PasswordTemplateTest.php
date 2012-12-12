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
 *  Forgot Password templates
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_TransactionalEmails_PasswordTemplateTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_email_template');
    }

    /**
     * <p>Insert Variable in Forgot Password Template</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2068
     */
    public function insertVariableInForgotPasswordTemplate()
    {
        $this->clickButton('add_new_template');
        //Data
        $templateData = $this->loadDataSet('System', 'load_default_template');
        $variable = 'reset_password_url_variable';
        //Steps
        $this->fillFieldset($templateData, 'load_default_template');
        $this->clickButton('load_template', false);
        $this->waitForAjax();
        $this->transactionalEmailsHelper()->insertVariable($variable);
    }

    /**
     * <p>Create new Admin Password Template</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2067
     */
    public function newForgotAdminPasswordTemplate()
    {
        //Data
        $templateData = $this->loadDataSet('System', 'load_default_admin_template');
        $templateInformation = $this->loadDataSet('System', 'admin_template_information');
        $templateName = array('template_name' => $templateInformation['template_name']);
        $variable = 'reset_password_url_variable';
        //Verifying
        $this->transactionalEmailsHelper()->createNewTemplate($templateData, $templateName, $variable);
        $this->assertMessagePresent('success', 'success_create_template');
    }

    /**
     * <p>Create new Password Reset template</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2070
     */
    public function newForgotCustomerPasswordTemplate()
    {
        //Data
        $templateData = $this->loadDataSet('System', 'load_default_template');
        $templateInformation = $this->loadDataSet('System', 'template_information');
        $templateName = array('template_name' => $templateInformation['template_name']);
        $variable = 'reset_password_url_variable';
        //Verification
        $this->transactionalEmailsHelper()->createNewTemplate($templateData, $templateName, $variable);
        $this->assertMessagePresent('success', 'success_create_template');
    }
}