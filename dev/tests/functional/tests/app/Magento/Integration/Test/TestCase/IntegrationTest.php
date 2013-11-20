<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\TestCase;

use Mtf\Factory\Factory;
use Magento\Integration\Test\Repository\Integration as IntegrationRepository;
use Magento\Integration\Test\Fixture\Integration as IntegrationFixture;

/**
 * Integration functionality verification
 */
class IntegrationTest extends \Mtf\TestCase\Functional
{
    /**
     * Login into backend area before tests.
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Create new Integration with valid data
     *
     * @ZephyrId MAGETWO-16694
     */
    public function testCreateIntegration()
    {
        //Data
        $integrationFixture = Factory::getFixtureFactory()->getMagentoIntegrationIntegration();
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_TAB);
        //Steps
        $newIntegrationPage = Factory::getPageFactory()->getAdminIntegrationNew();
        $newIntegrationPage->open();
        $newIntegrationPage->getIntegrationFormBlock()->fill($integrationFixture)->save($integrationFixture);
        //Verification
        $this->_checkSaveSuccessMessage();
        $this->_ensureMatchingIntegrationData($integrationFixture);
    }

    /**
     * Edit Integration
     *
     * @ZephyrId MAGETWO-16759
     */
    public function testEditIntegration()
    {
        //Precondition
        $integrationFixture = Factory::getFixtureFactory()->getMagentoIntegrationIntegration();
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_TAB);
        $integrationFixture->persist();
        //Steps
        $editIntegrationPage = Factory::getPageFactory()->getAdminIntegrationEdit();
        $this->_openByName($integrationFixture->getName());
        $editForm = $editIntegrationPage->getIntegrationFormBlock();
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_TAB);
        $editForm->update($integrationFixture)->save($integrationFixture);
        //Verification
        $this->_checkSaveSuccessMessage();
        $this->_ensureMatchingIntegrationData($integrationFixture);
    }

    /**
     * Navigate to the Integration page from Edit Integration page
     *
     * @param IntegrationFixture $integrationFixture injectable
     *
     * @ZephyrId MAGETWO-16823
     */
    public function testNavigation(IntegrationFixture $integrationFixture)
    {
        //Preconditions
        $integrationFixture->persist();
        //Steps
        $editIntegrationPage = Factory::getPageFactory()->getAdminIntegrationEdit();
        $this->_openByName($integrationFixture->getName());
        $editIntegrationPage->getIntegrationFormBlock()->back();
        //Verification
        $this->assertTrue(
            Factory::getPageFactory()->getAdminIntegration()->getGridBlock()->isVisible(),
            'Integration grid is not visible'
        );
    }

    /**
     * Api tab verification
     *
     * @ZephyrId MAGETWO-17045
     */
    public function testApiTabVerification()
    {
        //Data
        $integrationFixture = Factory::getFixtureFactory()->getMagentoIntegrationIntegration();
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_TAB);
        //Steps
        $newIntegrationPage = Factory::getPageFactory()->getAdminIntegrationNew();
        $newIntegrationPage->open();
        $newIntegrationPage->getIntegrationFormBlock()->fill($integrationFixture);
        $newIntegrationPage->getIntegrationFormBlock()->openApiTab();
        $this->assertTrue($newIntegrationPage->getApiTab()->isResourceVisible(), 'Resources tree should be visible.');
        $newIntegrationPage->getApiTab()->changeRoleAccess('All');
        $this->assertFalse($newIntegrationPage->getApiTab()->isResourceVisible(),
            'Resources tree should not be visible.'
        );
        $newIntegrationPage->getIntegrationFormBlock()->save($integrationFixture);
        //Verification
        $this->_checkSaveSuccessMessage();
        $this->_openByName($integrationFixture->getName());
        $newIntegrationPage->getIntegrationFormBlock()->openApiTab();
        $this->assertTrue($newIntegrationPage->getApiTab()->isResourceVisible(), 'Resources tree should be visible.');
    }

    /**
     * Check success message after integration save.
     */
    protected function _checkSaveSuccessMessage()
    {
        $this->assertTrue(
            Factory::getPageFactory()->getAdminIntegration()->getMessageBlock()->waitForSuccessMessage(),
            'Integration save success message was not found.'
        );
    }

    /**
     * Check integration data
     *
     * @param IntegrationFixture $integrationFixture
     */
    protected function _ensureMatchingIntegrationData(IntegrationFixture $integrationFixture)
    {
        $editIntegrationPage = Factory::getPageFactory()->getAdminIntegrationEdit();
        $this->_openByName($integrationFixture->getName());
        $editIntegrationPage->getIntegrationFormBlock()->verify($integrationFixture);
    }

    /**
     * Open existing integration page by integration name.
     *
     * @param string $integrationName
     */
    protected function _openByName($integrationName)
    {
        $integrationGridPage = Factory::getPageFactory()->getAdminIntegration();
        $integrationGridPage->open();
        $integrationGridPage->getGridBlock()->searchAndOpen(array('name' => $integrationName));
    }
}
