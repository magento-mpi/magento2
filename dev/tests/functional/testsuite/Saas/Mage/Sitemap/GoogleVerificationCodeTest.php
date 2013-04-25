<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @method Saas_Mage_SaasAdminMenu_Helper saasAdminMenuHelper() saasAdminMenuHelper()
 */
class Saas_Mage_Sitemap_GoogleVerificationCodeTest extends Saas_Mage_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * @test
     * @group goinc-dasha
     */
    public function checkSroreConfigurationXmlSitemapVerificationOptionIsPresent()
    {
        $this->navigate('system_configuration');

        $this->systemConfigurationHelper()->openConfigurationTab('catalog_google_sitemap');
        $this->systemConfigurationHelper()->expandFieldSet('generation_settings');


        //sleep(15);
        echo $this->_getControlXpath('field','google_verification_code');
        exit;
        $this->fillField('google_verification_code', '123456');
        $this->clickButton('save_config');
    }
}