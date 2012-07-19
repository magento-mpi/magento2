<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_NewsletterAdmin
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test adding new Template.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_NewsletterAdmin_AddTemplateTest extends Mage_Selenium_TestCase
{

    /**
     * Test creation newsletter template
     *
     * @package     selenium
     * @subpackage  tests
     * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('newsletter_templates');
    }

    /**
     * <p>Test navigation.</p>
     * <p>Steps:</p>
     * <p>1. Verify that 'Add New Template' button is present and click it.</p>
     * <p>2. Verify that the New Newsletter Template page is opened.</p>
     * <p>3. Verify that 'Back' button is present.</p>
     * <p>4. Verify that 'Reset' button is present.</p>
     * <p>5. Verify that 'Convert to Plain Text' button is present.</p>
     * <p>3. Verify that 'Preview Template' button is present.</p>
     * <p>3. Verify that 'Save Template' button is present.</p>
     *
     * @test
     *
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
     * <p>Steps:</p>
     * <p>1. Click 'Add New Template' button.</p>
     * <p>2. Fill in required fields.</p>
     * <p>3. Click 'Save Template' button.</p>
     * <p>Expected result:</p>
     * <p>Template is created.</p>
     * <p>Success Message is displayed</p>
     *
     * @test
     *
     */
    public function withRequiredFields()
    {
        //Data
        $templateData = $this->loadDataSet('Newsletter', 'generic_template');
        //Steps
        $this->newsletterAdminHelper()->createNewsletterTemplate($templateData);
        //Verifying
        $this->validatePage('newsletter_templates');
    }

    /**
     * <p>Create New Newsletter Template. Fill in all required fields except one field.</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add New Template' button.</p>
     * <p>2. Fill in required fields except one field.</p>
     * <p>3. Click 'Save Template' button.</p>
     * <p>Expected result:</p>
     * <p>Newsletter Template is not created.</p>
     * <p>Error Message is displayed.</p>
     *
     * @param $emptyField
     *
     * @test
     *
     * @dataProvider withRequiredFieldsEmptyDataProvider
     *
     */
    public function withRequiredFieldsEmpty($emptyField)
    {
        //Data
        $templateData = $this->loadDataSet('Newsletter', 'generic_template', array($emptyField => '%noValue%'));
        //Steps
        $this->newsletterAdminHelper()->createNewsletterTemplate($templateData);

        //Verifying
        $xpath = $this->_getControlXpath('field', $emptyField);
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('error', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
          array('template_name'),
          array('template_subject'),);
    }
}
