<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_SystemConfiguration
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Template Settings tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Community2_Mage_SystemConfiguration_TemplateSettingsTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    /**
     * <p>Apply different Email Sender</p>
     * <p>Steps:</p>
     * <p>1.Go to System - Configuration - ADVANCED - Admin - Admin User Emails.</p>
     * <p>2.Click on the "Forgot Password Email Sender" drop-down.</p>
     * <p>3.Select required email sender and save changes. </p>
     * <p>4.Request password reset for required admin user and check for email sender.</p>
     * <p>Expected result:</p>
     * <p>Email sender is those that was selected.</p>
     *
     * @dataProvider diffEmailSendersAdminTemplateDataProvider
     *
     * @param string $emailSender
     *
     * @test
     * @TestlinkId TL-MAGE-2078
     */
    public function diffEmailSendersAdminTemplate($emailSender)
    {
        //Steps
        $this->navigate('system_configuration');
        //Verification
        $this->systemConfigurationHelper()->configure($this->loadDataSet('Advanced', 'admin_user_emails_settings',
            array('forgot_password_email_sender' => $emailSender)));
    }

    public function diffEmailSendersAdminTemplateDataProvider()
    {
        return array(
            array('General Contact'),
            array('Sales Representative'),
            array('Customer Support'),
            array('Custom Email 1'),
            array('Custom Email 2'));
    }

    /**
     * <p>Change Recovery Link Expiration Period</p>
     * <p>Steps:</p>
     * <p>1.Go to System - Configuration - ADVANCED - Admin - Admin User Emails.</p>
     * <p>2.Change Recovery Link Expiration Period(input wrong value)</p>
     * <p>3.Try to save changes</p>
     * <p>Expected result:</p>
     * <p>Appropriate messages are displayed.</p>
     *
     * @dataProvider diffExpirationPeriodAdminTemplateDataProvider
     *
     * @param string $expirationPeriod
     *
     * @test
     * @TestlinkId TL-MAGE-2079
     */
    public function diffExpirationPeriodAdminTemplate($expirationPeriod)
    {
        //Data
        $config = $this->loadDataSet('Advanced', 'admin_user_emails_settings',
            array('recovery_link_exp_period' => $expirationPeriod));
        //Steps
        $this->navigate('system_configuration');
        //Verification
        if ($expirationPeriod == 'a' || $expirationPeriod == '1.1') {
            $this->setExpectedException('PHPUnit_Framework_AssertionFailedError',
                '"Recovery Link Expiration Period (days)": Please use numbers only in this field. '.
                'Please avoid spaces or other characters such as dots or commas.');
        } elseif ($expirationPeriod == '0') {
            $this->setExpectedException('PHPUnit_Framework_AssertionFailedError',
                '"Recovery Link Expiration Period (days)": The value is not within the specified range.');
        }
        $this->systemConfigurationHelper()->configure($config);
    }

    public function diffExpirationPeriodAdminTemplateDataProvider()
    {
        return array(
            array('a'),
            array('1.1'),
            array('0'));
    }

    /**
     * <p>Use new custom email template for reset admin password</p>
     * <p>Steps:</p>
     * <p>1.Go to System - Transactional Emails</p>
     * <p>2.Click on the "Add New Template" button and select "Forgot Admin Password Template" on the "Template" drop-down</p>
     * <p>3.Change template and save this changes</p>
     * <p>4.Go to System - Configuration - ADVANCED - Admin - Admin User Emails</p>
     * <p>5.Select required email template in "Forgot Password Email Template" and save configuration. </p>
     * <p>Expected result:</p>
     * <p>New template is applied and configuration is successfully saved.<p>
     *
     * @test
     * @TestlinkId TL-MAGE-2073
     */
    public function newAdminEmailTemplate()
    {
        //Data
        $templateData = $this->loadDataSet('System', 'load_default_admin_template');
        $templateInformation = $this->loadDataSet('System', 'admin_template_information');
        $templateName = array('template_name' => $templateInformation['template_name']);
        //Steps
        $this->navigate('system_email_template');
        $this->transactionalEmailsHelper()->createNewTemplate($templateData, $templateName, '');
        //Verification
        $this->assertMessagePresent('success', 'success_create_template');
        //Steps
        $this->navigate('system_configuration');
        //Verification
        $this->systemConfigurationHelper()->configure($this->loadDataSet('Advanced', 'admin_user_emails_settings',
            array('forgot_password_email_template' => $templateInformation['template_name'])));
    }

    /**
     * <p>Apply different Email Sender</p>
     * <p>Steps:</p>
     * <p>1.Go to System - Configuration - CUSTOMERS - Customer Configuration - Password Options.</p>
     * <p>2.Click on the "Forgot Password Email Sender" drop-down.</p>
     * <p>3.Select required email sender and save changes. </p>
     * <p>4.Request password reset for required admin user and check for email sender.</p>
     * <p>Expected result:</p>
     * <p>Email sender is those that was selected.<p>
     *
     * @dataProvider diffEmailSendersForCustomerTemplateDataProvider
     *
     * @param string $emailSender
     *
     * @test
     * @TestlinkId TL-MAGE-2072
     */
    public function diffEmailSendersForCustomerTemplate($emailSender)
    {
        //Steps
        $this->navigate('system_configuration');
        //Verification
        $this->systemConfigurationHelper()->configure($this->loadDataSet('Advanced',
            'customer_configuration_password_options', array('forgot_email_sender' => $emailSender)));
    }

    public function diffEmailSendersForCustomerTemplateDataProvider()
    {
        return array(
            array('General Contact'),
            array('Sales Representative'),
            array('Customer Support'),
            array('Custom Email 1'),
            array('Custom Email 2'));
    }

    /**
     * <p>Admin can change Recovery Link Expiration Period for Customer Forgot Password Template</p>
     * <p>Steps:</p>
     * <p>1.Go to System - Configuration - CUSTOMERS - Customer Configuration - Password Options.</p>
     * <p>2.Change Recovery Link Expiration Period(input wrong value).</p>
     * <p>3.Try to save changes.</p>
     * <p>Expected result:</p>
     * <p>Appropriate messages are displayed.</p>
     *
     * @dataProvider diffExpirationPeriodForCustomerTemplateDataProvider
     *
     * @param string $expirationPeriod
     *
     * @test
     * @TestlinkId TL-MAGE-2090
     */
    public function diffExpirationPeriodForCustomerTemplate($expirationPeriod)
    {
        //Data
        $config = $this->loadDataSet('Advanced', 'customer_configuration_password_options',
            array('recovery_link_exp_period' => $expirationPeriod));
        //Steps
        $this->navigate('system_configuration');
        //Verification
        if ($expirationPeriod == 'a' || $expirationPeriod == '1.1') {
            $this->setExpectedException('PHPUnit_Framework_AssertionFailedError',
                '"Recovery Link Expiration Period (days)": Please use numbers only in this field. '.
                'Please avoid spaces or other characters such as dots or commas.');
        } elseif ($expirationPeriod == '0') {
            $this->setExpectedException('PHPUnit_Framework_AssertionFailedError',
                '"Recovery Link Expiration Period (days)": The value is not within the specified range.');
        }
        $this->systemConfigurationHelper()->configure($config);
    }

    public function diffExpirationPeriodForCustomerTemplateDataProvider()
    {
        return array(
            array('a'),
            array('1.1'),
            array('0'));
    }

    /**
     * <p>Use new custom email template for reset customer password</p>
     * <p>Steps:</p>
     * <p>1.Go to System - Transactional Emails</p>
     * <p>2.Click on the "Add New Template" button and select "Forgot Password on the "Template" drop-down</p>
     * <p>3.Change template and save this changes</p>
     * <p>4.Go to System - Configuration - CUSTOMERS - Customer Configuration - Password Options</p>
     * <p>5.Select required email template in "Forgot Email Template"/"Remind Email Template" and save configuration. </p>
     * <p>Expected result</p>
     * <p>New template is applied and configuration is successfully saved.<p>
     *
     * @dataProvider diffEmailTemplatesDataProvider
     * @param string $emailTemplate
     * @test
     * @TestlinkId TL-MAGE-2046, TL-MAGE-2063
     *
     */
    public function diffEmailTemplates($emailTemplate)
    {
        //Data
        $templateData = $this->loadDataSet('System', 'load_default_template');
        $templateInformation = $this->loadDataSet('System', 'template_information');
        $templateName = array('template_name' => $templateInformation['template_name']);
        //Steps
        $this->navigate('system_email_template');
        $this->transactionalEmailsHelper()->createNewTemplate($templateData, $templateName, '');
        //Verification
        $this->assertMessagePresent('success', 'success_create_template');
        //Steps
        $this->navigate('system_configuration');
        //Verification
        $this->systemConfigurationHelper()->configure($this->loadDataSet('Advanced',
            'customer_configuration_password_options', array($emailTemplate => $templateInformation['template_name'])));
    }

    public function diffEmailTemplatesDataProvider()
    {
        return array(
            array('forgot_email_template'),
            array('remind_email_template'));
    }
}