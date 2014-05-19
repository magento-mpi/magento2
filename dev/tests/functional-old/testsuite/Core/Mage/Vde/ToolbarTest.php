<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Core_Mage_Vde_ToolbarTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }

    public function tearDownAfterTestClass()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/enable_secret_key');
        $this->logoutAdminUser();
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
          $this->window(null);
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-6507
     * @author roman.grebenchuk
     */
    public function pageTypeSelector()
    {
        $this->addParameter('themeId','1');
        $this->admin('vde_design');
        $this->vdeHelper()->selectPageHandle('OAuth authorization for customer');
        //verify iframe content
        $this->addParameter('wrappedPage', 'oauth-authorize-index');
        $this->frame('vde_container_frame');
        $this->assertTrue($this->controlIsPresent('field', 'body'));
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-5652
     * @author iuliia.babenko
     */
    public function highlightTest()
    {
        $this->addParameter('themeId','1');
        $this->admin('vde_design');
        // Verify that highlight is enabled and applied by default
        $this->assertTrue($this->vdeHelper()->isHighlightEnabled(), 'Highlight is not enabled by default');
        $this->frame('vde_container_frame');
        $this->assertTrue($this->vdeHelper()->areHighlightBlocksShown(), 'Blocks are not highlighted');
        $this->window(null);

        $this->vdeHelper()->disableHighlight();
        // Verify that containers are not highlighted
        $this->frame('vde_container_frame');
        $this->assertFalse($this->vdeHelper()->areHighlightBlocksShown(),
            'Blocks are still highlighted after disable highlight');
    }
}
