<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @method Saas_Mage_SaasAdminMenu_Helper saasAdminMenuHelper() saasAdminMenuHelper()
 */
class Saas_Magento_Downloadable_FunctionalityLimitationTest extends Saas_Mage_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * @test
     * @group goinc
     */
    public function checkThatDisabledElementsAreNotPresentInTopMenu()
    {
        $this->saasAdminMenuHelper()->clickMainMenuElement('Reports');

        $this->assertFalse($this->saasAdminMenuHelper()->isSubMenuElementPresent('Downloads'));
    }

    /**
     * @test
     * @group goinc
     */
    public function checkThatFunctionalityIsNotAvailableByDirectLink()
    {
        $this->navigate('report_product_downloads', false);

        $this->assertFalse($this->isHeaderPresent('Downloads'));
        $this->assertFalse($this->controlIsPresent('pageelement', 'grid'));
    }

    /**
     * @test
     * @group goinc
     */
    public function checkThatDisabledElementsAreNotPresentInSystemConfiguration()
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('catalog_catalog');

        $this->assertFalse($this->controlIsPresent('fieldset', 'downloadable_product_options'));
    }

    /**
     * @test
     * @group goinc
     */
    public function checkThatDownloadableTypeIsNotExistInProductTypeDropdown()
    {
        $this->navigate('manage_products');
        $this->addParameter('productType', 'Downloadable');

        $this->assertFalse($this->controlIsPresent('button', 'add_product_by_type'));
    }
}
