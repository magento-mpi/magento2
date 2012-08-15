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
class Community2_Mage_TransactionalEmails_PasswordTemplateTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_email_template');
    }

    /**
     * <p>Test navigation.</p>
     * <p>Steps:</p>
     * <p>1. Verify that 'Add New Template' button is present and click her.</p>
     * <p>2. Verify that the create new tem[late page is opened.</p>
     * <p>3. Verify that 'Back' button is present.</p>
     * <p>4. Verify that 'Reset' button is present.</p>
     * <p>5. Verify that 'Convert to Plain Text' button is present.</p>
     * <p>6. Verify that 'Preview Template' button is present.</p>
     * <p>7. Verify that 'Save Template' button is present.</p>
     * <p>8. Verify that 'Load Default Template' fieldset with required fields is present.</p>
     * <p>9. Verify that 'Template Information' fieldset with required fields is present.</p>
     *
     * @test
     */
    public function navigation()
    {
        $this->assertTrue($this->buttonIsPresent('add_new_template'),
            'There is no "Add New Template" button on the page');
        $this->clickButton('add_new_template');
        $this->assertTrue($this->checkCurrentPage('new_email_template'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('convert_to_plain_text'),
            'There is no "Convert to Plain Text" button on the page');
        $this->assertTrue($this->buttonIsPresent('preview_template'),
            'There is no "Preview Template" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_template'), 'There is no "Save Template" button on the page');
        $this->assertTrue($this->controlIsPresent('fieldset', 'load_default_template'),
            'There is no "Load default template" fieldset on the page');
        $this->assertTrue($this->controlIsPresent('fieldset', 'template_information'),
            'There is no "Template Information" fieldset on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'template'),
            'There is no "Template" dropdown on the page');
        $this->assertTrue($this->buttonIsPresent('load_template'), 'There is no "Load Template" button on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'template'),
            'There is no "Template" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('field', 'template_name'),
            'There is no "Template Name" field on the page');
        $this->assertTrue($this->controlIsPresent('field', 'template_subject'),
            'There is no "Template Subject" field on the page');
        $this->assertTrue($this->buttonIsPresent('insert_variable'),
            'There is no "Insert Variable" button on the page');
        $this->assertTrue($this->controlIsPresent('field', 'template_content'),
            'There is no "Template Content" textarea on the page');
        $this->assertTrue($this->controlIsPresent('field', 'template_styles'),
            'There is no "Template Styles" textarea on the page');
    }

    /**
     * <p>Insert Variable in Forgot Password Template</p>
     * <p>Steps:</p>
     * <p>1.Go to System - Transactional Emails.</p>
     * <p>2.Click on the "Add New Template" button.</p>
     * <p>3.Verify that "Forgot Password" item is displayed on the "Template" dropdown.</p>
     * <p>4.Select appropriate drop-down item and click on the "Load Template" button</p>
     * <p>5.Click on the "Insert Variable" button and verify that "Reset Password URL" is displayed on the "Template Variables" tab<//p>
     * <p>Expected result</p>
     * <p>"Reset Password URL" is displayed on the "Template Variables" tab.</p>
     *
     * @depends navigation
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
     * <p>Steps:</p>
     * <p>1.Go to System - Transactional Emails.</p>
     * <p>2.Click on the "Add New Template" button.</p>
     * <p>3.Select Forgot Admin Password drop-down item and click on the "Load Template" button</p>
     * <p>4.Click on the "Insert Variable" button <//p>
     * <p>5.Click on the Reset Password URL </p>
     * <p>6.Enter name for template and save it.
     * <p>Expected result</p>
     * <p>Template is successfully saved.</p>
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
     * <p>Steps:</p>
     * <p>1.Go to System - Transactional Emails.</p>
     * <p>2.Click on the "Add New Template" button.</p>
     * <p>3.Select Forgot Password drop-down item and click on the "Load Template" button</p>
     * <p>4.Click on the "Insert Variable" button <//p>
     * <p>5.Click on the Reset Password URL </p>
     * <p>6.Enter name for template and save it.
     * <p>Expected result</p>
     * <p>Template is successfully saved.</p>
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