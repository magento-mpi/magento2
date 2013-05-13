<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_MageLocale_LocaleTest extends Saas_Mage_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * @test
     * @group goinc
     */
    public function checkLocalesInAccountSettings()
    {
        $this->navigate('my_account');

        $this->waitForControl('dropdown', 'interface_locale');
        $select = $this->getControlSelect('interface_locale');

        $this->assertEquals(array('en_US'), $select->selectOptionValues());
        $this->assertEquals(array('English (United States) / English (United States)'), $select->selectOptionLabels());
    }

    /**
     * @test
     * @group goinc
     */
    public function checkLocalesInSystemConfigurationLocaleOptions()
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('general_general');
        $this->systemConfigurationHelper()->expandFieldSet('locale_options');

        $this->waitForControl('dropdown', 'locale');
        $select = $this->getControlSelect('locale');

        $this->assertEquals(array('en_US'), $select->selectOptionValues());
        $this->assertEquals(array('English (United States)'), $select->selectOptionLabels());
    }
}
