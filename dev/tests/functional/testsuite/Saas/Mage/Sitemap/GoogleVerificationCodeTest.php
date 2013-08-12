<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Magento_Sitemap_GoogleVerificationCodeTest extends Saas_Mage_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();

        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('catalog_xml_sitemap');
        $this->systemConfigurationHelper()->expandFieldSet('generation_settings');
    }

    /**
     * @test
     * @group goinc
     */
    public function checkFillingVerificationCode()
    {
        // Fill value in backend
        $value = 'some-code-' . $this->generate('string', 5);
        $this->fillField('google_verification_code', $value);
        $this->clickButton('save_config');
        $this->assertMessagePresent('success', 'success_saved_config');

        // Check result on frontend
        $this->frontend();
        $this->assertEquals($value, $this->getControlAttribute('pageelement', 'google_site_verification', 'content'));
    }

    /**
     * @test
     * @group goinc
     */
    public function checkClearingVerificationCode()
    {
        // Fill value in backend
        $this->fillField('google_verification_code', '');
        $this->clickButton('save_config');
        $this->assertMessagePresent('success', 'success_saved_config');

        // Check result on frontend
        $this->frontend();
        $this->assertFalse($this->elementIsPresent('pageelement', 'google_site_verification'));
    }
}
