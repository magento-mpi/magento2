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
class Core_Mage_TransactionalEmails_TransactionalEmailsTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_email_template');
    }

    /**
     * <p>Test navigation.</p>
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
        $this->transactionalEmailsHelper()->checkControlsPresence('fieldset', array(
                                                                              'load_default_template',
                                                                              'template_information')
        );
        $dropdownTrue = array('template');
        $this->transactionalEmailsHelper()->checkControlsPresence('dropdown', $dropdownTrue);
        $this->transactionalEmailsHelper()->checkControlsPresence(
            'field',
            array('template_name', 'template_subject', 'template_content', 'template_styles')
        );
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Verification for empty fields</p>
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
        $templateInformation =
            $this->loadDataSet('System', 'new_template_information', array($emptyRequiredField => '%noValue%'));
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
     *
     * @test
     */
    public function deleteTemplate()
    {
        //Data
        $templateData = $this->loadDataSet('System', 'new_load_default_template');
        $templateInformation = $this->loadDataSet('System', 'new_template_information');
        $templateName = array('template_name' => $templateInformation['template_name']);
        $searchData = $this->loadDataSet('System', 'search_template_data',
            array('filter_template_name' => $templateInformation['template_name']));
        $this->addParameter('elementTitle', $templateInformation['template_name']);
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
     *
     * @test
     */
    public function editTemplate()
    {
        //Data
        $templateData = $this->loadDataSet('System', 'new_load_default_template');
        $templateInformation = $this->loadDataSet('System', 'new_template_information');
        $templateName = array('template_name' => $templateInformation['template_name']);
        $searchData = $this->loadDataSet('System', 'search_template_data',
            array('filter_template_name' => $templateInformation['template_name']));
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
     *
     * @test
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
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
        $this->assertTrue($this->buttonIsPresent('return_html_version'),
            'There is no "Return HTML version " button on the page');
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