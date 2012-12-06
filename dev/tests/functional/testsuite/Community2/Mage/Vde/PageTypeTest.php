<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Community2_Mage_Vde_PageTypeTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }

    public function tearDownAfterTestClass()
    {
        $this->selectWindow('null');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/enable_secret_key');
        $this->logoutAdminUser();
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
          $this->selectWindow('null');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-6507
     * @author roman.grebenchuk
     */
    public function pageTypeSelector()
    {
        $this->admin('vde_design');
        $this->vdeHelper()->selectPageHandle('OAuth authorization for customer');
        //verify iframe content
        $this->addParameter('wrappedPage', 'oauth-authorize-index');
        $this->selectFrame('vde_container_frame');
        $this->assertTrue($this->controlIsPresent('field', 'body'));
        $this->selectWindow('null');
    }
}
