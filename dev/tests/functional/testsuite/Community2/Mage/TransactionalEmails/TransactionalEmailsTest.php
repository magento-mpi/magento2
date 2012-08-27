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
 *  Transactional Emails tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_TransactionalEmails_TransactionalEmailsTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_email_template');
    }

    /**
     * <p>Test navigation.</p>
     * <p>Steps:</p>
     * <p>1.Verify that 'Add New Template' button is present and click her.</p>
     * <p>2.Verify that the create new template page is opened.</p>
     * <p>3.Verify that 'Back' button is present.</p>
     * <p>4.Verify that 'Reset' button is present.</p>
     * <p>5.Verify that 'Convert to Plain Text' button is present.</p>
     * <p>6.Verify that 'Preview Template' button is present.</p>
     * <p>7.Verify that 'Save Template' button is present.</p>
     * <p>8.Verify that 'Load Default Template' fieldset with required fields is present.</p>
     * <p>9.Verify that 'Template Information' fieldset with required fields is present.</p>
     *
     * @test
     */
    public function navigation()
    {
        $buttonsTrue = array('add_new_template');
        foreach ($buttonsTrue as $button) {
            if (!$this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is not present on the page");
            }
        }
        $this->clickButton('add_new_template');
        $buttonsTrue =
            array('back', 'reset', 'convert_to_plain_text', 'preview_template', 'save_template', 'load_template',
                'insert_variable');
        foreach ($buttonsTrue as $button) {
            if (!$this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is not present on the page");
            }
        }
        $fieldSetTrue = array('load_default_template', 'template_information');
        foreach ($fieldSetTrue as $fieldSet) {
            if (!$this->controlIsPresent('fieldset', $fieldSet)) {
                $this->addVerificationMessage("Fieldset $fieldSet is not present on the page");
            }
        }
        $dropdownTrue = array('template');
        foreach ($dropdownTrue as $dropdown) {
            if (!$this->controlIsPresent('dropdown', $dropdown)) {
                $this->addVerificationMessage("Dropdown $dropdown is not present on the page");
            }
        }
        $fieldsTrue = array('template_name', 'template_subject', 'template_content', 'template_styles');
        foreach ($fieldsTrue as $field) {
            if (!$this->controlIsPresent('field', $field)) {
                $this->addVerificationMessage("Field $field is not present on the page");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Verification for empty fields</p>
     * <p>Steps:</p>
     * <p>1.Go to System - Transactional Emails.</p>
     * <p>2.Click on the "Add New Template" button.</p>
     * <p>3.Click on the "Load Template" button</p>
     * <p>Expected result</p>
     * <p>"This is required field" message is displayed below the field</p>
     * <p>4.Reset form and click on the "Save Template" button.</p>
     * <p>Expected result</p>
     * <p>"This is required field" message is displayed below all required fields</p>
     *
     * @dataProvider requiredFieldDataProvider
     *
     * @param string $emptyRequiredField
     * @param string $emptyAssociatedField
     *
     * @test
     */
    public function testRequiredField($emptyRequiredField, $emptyAssociatedField)
    {
        //Data
        $templateInformation = $this->loadDataSet('System', 'new_template_information', array($emptyRequiredField => '%noValue%'));
        //Steps
        $this->clickButton('add_new_template');
        $this->clickButton('load_template', false);
        $this->addParameter('field', 'template_select');
        //Verifying
        $this->assertMessagePresent('validation', 'required_field_message');
        //Steps
        $this->clickButton('reset', false);
        $this->waitForPageToLoad();
        $this->transactionalEmailsHelper()->fillTemplateForm($templateInformation, 'template_information');
        $this->clickButton('save_template', false);
        $this->waitForAjax();
        $this->addParameter('field', $emptyAssociatedField);
        //Verifying
        $this->assertMessagePresent('validation', 'required_field_message');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function requiredFieldDataProvider()
    {
        return array(
            array('template_name', 'template_code'),
            array('template_subject', 'template_subject'),
            array('template_content', 'template_text')
        );
    }

    /**
     * <p>Create new Template</p>
     * <p>Steps:</p>
     * <p>1.Go to System - Transactional Emails.</p>
     * <p>2.Click on the "Add New Template" button.</p>
     * <p>3.Select any item drop-down item and click on the "Load Template" button</p>
     * <p>4.Enter name for template and save it.</p>
     * <p>Expected result</p>
     * <p>Template is successfully saved.</p>
     *
     * @test
     */
    public function createNewTemplate()
    {
        //Data
        $templateData = $this->loadDataSet('System', 'new_load_default_template');
        $templateInformation = $this->loadDataSet('System', 'new_template_information');
        $templateName = array('template_name' => $templateInformation['template_name']);
        //Verifying
        $this->transactionalEmailsHelper()->createNewTemplate($templateData, $templateName, '');
        $this->assertMessagePresent('success', 'success_create_template');
    }

    /**
     * <p>Delete template.</p>
     * <p>Steps:</p>
     * <p>1. Login to backend and go to System - Transactional Emails</p>
     * <p>2. Create new template</p>
     * <p>3. Open new template and click on the "Delete Template" button</p>
     * <p>Expected result:</p>
     * <p>Template was deleted successfully. Success message is displayed.</p>
     *
     * @test
     */
    public function deleteTemplate()
    {
        //Data
        $templateData = $this->loadDataSet('System', 'new_load_default_template');
        $templateInformation = $this->loadDataSet('System', 'new_template_information');
        $templateName = array('template_name' => $templateInformation['template_name']);
        $searchData = $this->loadDataSet('System', 'search_template_data', array('filter_template_name' => $templateInformation['template_name']));
        $this->addParameter('template_name', $templateInformation['template_name']);
        //Steps
        $this->transactionalEmailsHelper()->createNewTemplate($templateData, $templateName, '');
        //Verifying
        $this->assertMessagePresent('success', 'success_create_template');
        //Steps
        $this->transactionalEmailsHelper()->deleteTemplate($searchData);
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_template');
    }

    /**
     * <p>Edit template.</p>
     * <p>Steps:</p>
     * <p>1. Login to backend and go to System - Transactional Emails</p>
     * <p>2. Create new template</p>
     * <p>3. Open new template and change required data</p>
     * <p>4. Click Save Template button </p>
     * <p>Expected result:</p>
     * <p>Template is saved successfully. Data are successfully changed.</p>
     *
     * @test
     */
    public function editTemplate()
    {
        //Data
        $templateData = $this->loadDataSet('System', 'new_load_default_template');
        $templateInformation = $this->loadDataSet('System', 'new_template_information');
        $templateName = array('template_name' => $templateInformation['template_name']);
        $searchData = $this->loadDataSet('System', 'search_template_data', array('filter_template_name' => $templateInformation['template_name']));
        $editData = $this->loadDataSet('System', 'edit_template_information');
        $this->addParameter('template_name', $templateInformation['template_name']);
        //Steps
        $this->transactionalEmailsHelper()->createNewTemplate($templateData, $templateName, '');
        //Verifying
        $this->assertMessagePresent('success', 'success_create_template');
        //Steps
        $this->transactionalEmailsHelper()->editTemplate($searchData, $editData);
        //Verifying
        $this->assertMessagePresent('success', 'success_create_template');
    }

    /**
     * <p>Test content buttons('Back', 'Reset', 'Convert to Plain Text') </p>
     * <p>Steps:</p>
     * <p>1.Go to System - Transactional Emails.</p>
     * <p>2.Click on the 'Back' button.</p>
     * <p>Expected result: </p>
     * <p>Transactional Emails page is displayed.</p>
     * <p>3.Go to add new template page and click on the 'Convert to Plain Text'</p>
     * <p>Expected result: </p>
     * <p>Confirmation pop-up is displayed</p>
     * <p>4.Click on 'Yes' button </p>
     * <p>Expected result: </p>
     * <p>Page is reload. 'Reload Html Version' button is displayed instead of 'Convert to Plain Text' button</p>
     * <p>5.Set all required data and click on the 'Reset' button </p>
     * <p>Expected result</p>
     * <p>All fields are empty.</p>
     *
     * @test
     */
    public function contentButton()
    {
        //Steps
        $this->clickButton('add_new_template');
        //Verification
        $this->assertTrue($this->checkCurrentPage('new_email_template'), $this->getParsedMessages());
        //Steps
        $this->clickButton('back');
        $this->clickButton('add_new_template');
        //Verification
        if ($this->buttonIsPresent('convert_to_plain_text')) {
            $this->clickButtonAndConfirm('convert_to_plain_text', 'confirmation_convert_to_plain_text', false);
        }
        $this->assertTrue($this->buttonIsPresent('return_html_version'), 'There is no "Return HTML version " button on the page');
        //Data
        $templateData = $this->loadDataSet('System', 'new_load_default_template');
        $templateInformation = $this->loadDataSet('System', 'new_template_information');
        $templateName = array('template_name' => $templateInformation['template_name']);
        //Steps
        $this->transactionalEmailsHelper()->fillTemplateForm($templateData, 'load_default_template');
        $this->clickButton('load_template', false);
        $this->waitForAjax();
        $this->transactionalEmailsHelper()->fillTemplateForm($templateName, 'template_information');
        $this->clickButton('reset', false);
        $this->waitForPageToLoad();
        //Data
        $templateData = array('template' => '');
        //Verification
        $this->assertTrue($this->verifyForm($templateData), $this->getParsedMessages('verification'));
        //Data
        foreach ($templateInformation as $templateField => $templateValue) {
            $templateInformation[$templateField] = '';
        }
        //Verification
        $this->assertTrue($this->verifyForm($templateInformation), $this->getParsedMessages('verification'));
    }
}