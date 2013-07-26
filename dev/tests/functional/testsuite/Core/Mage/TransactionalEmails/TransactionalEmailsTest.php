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
    public function setUpBeforeTests()
    {
        $this->markTestIncomplete('MAGETWO-11337');
    }

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
        $this->transactionalEmailsHelper()->checkControlsPresence('button',
            array('add_new_template', 'reset_filter', 'search'));
        $this->clickButton('add_new_template');
        $this->transactionalEmailsHelper()->checkControlsPresence('button',
            array('back', 'reset', 'convert_to_plain_text', 'preview_template',
                'save_template', 'load_template', 'insert_variable'));
        $this->transactionalEmailsHelper()->checkControlsPresence('fieldset',
            array('load_default_template', 'template_information'));
        $this->transactionalEmailsHelper()->checkControlsPresence('dropdown', array('template'));
        $this->transactionalEmailsHelper()->checkControlsPresence('field',
            array('template_name', 'template_subject', 'template_content', 'template_styles'));
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Verification for empty fields</p>
     *
     * @dataProvider requiredFieldDataProvider
     *
     * @param string $emptyRequiredField
     *
     * @test
     */
    public function testRequiredField($emptyRequiredField)
    {
        //Data
        $templateInformation = $this->loadDataSet('System', 'template_information',
            array($emptyRequiredField => '%noValue%'));
        //Steps
        $this->transactionalEmailsHelper()->createEmailTemplate($templateInformation);
        //Verifying
        $this->addFieldIdToMessage('field', $emptyRequiredField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function requiredFieldDataProvider()
    {
        return array(
            array('template_name'),
            array('template_subject'),
            array('template_content')
        );
    }

    /**
     * <p>Create new Template</p>
     *
     * @return array
     * @test
     */
    public function createNewTemplate()
    {
        //Data
        $templateData = $this->loadDataSet('System', 'template_information',
            array('variable_1' => 'Base Secure URL'));
        //Verifying
        $this->transactionalEmailsHelper()->createEmailTemplate($templateData);
        $this->assertMessagePresent('success', 'success_create_template');

        return $this->loadDataSet('System', 'search_template_data', array(
            'filter_template_name' => $templateData['template_name'],
            'filter_subject' => $templateData['template_subject']
        ));
    }

    /**
     * <p>Edit template.</p>
     *
     * @param array $searchData
     *
     * @return array
     * @test
     * @depends createNewTemplate
     */
    public function editTemplate($searchData)
    {
        //Data
        $templateData = $this->loadDataSet('System', 'template_information',
            array('variable_1' => 'Base Unsecure URL'));
        //Steps
        $this->transactionalEmailsHelper()->editEmailTemplate($searchData, $templateData);
        //Verifying
        $this->assertMessagePresent('success', 'success_create_template');
        return $this->loadDataSet('System', 'search_template_data', array(
            'filter_template_name' => $templateData['template_name'],
            'filter_subject' => $templateData['template_subject']
        ));
    }

    /**
     * <p>Delete template.</p>
     *
     * @param array $searchData
     *
     * @test
     * @depends editTemplate
     */
    public function deleteTemplate($searchData)
    {
        $this->transactionalEmailsHelper()->deleteEmailTemplate($searchData);
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_template');
    }

    /**
     * <p>Test content buttons('Back', 'Reset', 'Convert to Plain Text') </p>
     *
     * @test
     */
    public function contentButton()
    {
        //Steps
        $this->clickButton('add_new_template');
        $this->assertTrue($this->checkCurrentPage('new_email_template'), $this->getParsedMessages());
        $this->clickButton('back');
        $this->assertTrue($this->checkCurrentPage('system_email_template'), $this->getParsedMessages());
        $this->clickButton('add_new_template');
        $this->clickButtonAndConfirm('convert_to_plain_text', 'confirmation_convert_to_plain_text', false);
        $this->assertTrue($this->buttonIsPresent('return_html_version'),
            'There is no "Return HTML version" button on the page');
        $this->clickButton('return_html_version', false);
        $this->assertTrue($this->buttonIsPresent('convert_to_plain_text'),
            'There is no "Convert to Plain Text" button on the page');
        $this->transactionalEmailsHelper()->fillEmailTemplateData(array('template' => 'Unsubscription Success'));
        $this->verifyForm(array(
            'template' => 'Unsubscription Success',
            'template_name' => '',
            'template_subject' => 'Newsletter unsubscription success',
            'template_content' => 'Newsletter unsubscription success'
        ));
        $this->assertEmptyVerificationErrors();
        $this->clickButton('reset');
        $this->verifyForm(array('template' => '', 'template_name' => '',
            'template_subject' => '', 'template_content' => ''));
        $this->assertEmptyVerificationErrors();
    }
}