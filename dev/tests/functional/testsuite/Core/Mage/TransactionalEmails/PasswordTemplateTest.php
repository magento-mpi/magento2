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
        //Data
        $templateData = $this->loadDataSet('System', 'load_forgot_password_template',
            array('variable_1' => 'Reset Password URL'));
        //Steps
        $this->clickButton('add_new_template');
        $this->transactionalEmailsHelper()->fillEmailTemplateData($templateData);
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
        $loadTemplate = $this->loadDataSet('System', 'load_forgot_admin_password_template',
            array('variable_1' => 'Reset Password URL', 'template_name' => 'New Admin Forgot Password'));
        //Verifying
        $this->transactionalEmailsHelper()->createEmailTemplate($loadTemplate);
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
        $loadTemplate = $this->loadDataSet('System', 'load_forgot_password_template',
            array('variable_1' => 'Reset Password URL', 'template_name' => 'New Customer Forgot Password'));
        //Verifying
        $this->transactionalEmailsHelper()->createEmailTemplate($loadTemplate);
        $this->assertMessagePresent('success', 'success_create_template');
    }
}