<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Newsletter
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test adding new Template.
 *
 * @package     Mage_Newsletter
 * @subpackage  functional_tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Newsletter_AddTemplateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     * <p>Navigate to Newsletter -> Newsletter Templates</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('newsletter_templates');
    }

    /**
     * <p>Test navigation.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6055
     */
    public function navigation()
    {
        $this->assertTrue($this->controlIsPresent('button', 'add_new_template'),
            'There is no "Add New Template" button on the page');
        $this->clickButton('add_new_template');
        $this->assertTrue($this->controlIsPresent('button', 'back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'convert_to_plain_text'),
            'There is no "Convert to Plain Text" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'preview_template'),
            'There is no "Preview Template" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'save_template'),
            'There is no "Save Template" button on the page');
    }

    /**
     * <p>Add Template. Fill in required fields.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6056
     */
    public function withRequiredFields()
    {
        //Data
        $templateData = 'Newsletter/generic_newsletter_data';
        //Steps
        $this->newsletterHelper()->createNewsletterTemplate($templateData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_newsletter');
    }

    /**
     * <p>Create New Newsletter Template. Fill in all required fields except one field.</p>
     *
     * @param $emptyField
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-6058
     */
    public function withRequiredFieldsEmpty($emptyField)
    {
        //Data
        $templateData = $this->loadDataSet('Newsletter', 'generic_newsletter_data', array($emptyField => ''));
        //Steps
        $this->newsletterHelper()->createNewsletterTemplate($templateData);

        //Verifying
        $this->addFieldIdToMessage('field', $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('newsletter_template_name'),
            array('newsletter_template_subject'),
            array('newsletter_template_sender_name'),
            array('newsletter_template_sender_email')
            );
    }

    /**
     * <p>Create New Newsletter Template. Fill in fields except 'Sender Email' by using special characters.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6059
     */
    public function withSpecialCharactersExceptSenderEmail()
    {
        //Data
        $templateData = $this->loadDataSet('Newsletter', 'generic_newsletter_data',
            array('newsletter_template_name'        => $this->generate('string', 32, ':punct:'),
                  'newsletter_template_subject'     => $this->generate('string', 32, ':punct:'),
                  'newsletter_template_sender_name' => $this->generate('string', 32, ':punct:')));
        //Steps
        $this->newsletterHelper()->createNewsletterTemplate($templateData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_newsletter');
    }

    /**
     * <p>Create Newsletter with invalid value for 'Sender Email' field</p>
     *
     * @param string $wrongEmail
     *
     * @test
     * @dataProvider withInvalidEmailDataProvider
     * @TestlinkId TL-MAGE-6060
     */
    public function withInvalidEmail($wrongEmail)
    {
        //Data
        $templateData = $this->loadDataSet('Newsletter', 'generic_newsletter_data',
            array('newsletter_template_sender_email' => $wrongEmail));
        //Steps
        $this->newsletterHelper()->createNewsletterTemplate($templateData);
        //Verifying
        $this->assertMessagePresent('error', 'invalid_email');
    }

    public function withInvalidEmailDataProvider()
    {
        return array(
            array('invalid'),
            array('test@invalidDomain'),
            array('te@st@unknown-domain.com')
        );
    }
}