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
        $this->markTestIncomplete('Obsolete selenium version on bamboo server');
        //Data
        $integrationFixture = Factory::getFixtureFactory()->getMagentoIntegrationIntegration();
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_TAB);
        //Steps
        $newIntegrationPage = Factory::getPageFactory()->getAdminIntegrationNew();
        $newIntegrationPage->open();
        $newIntegrationPage->getIntegrationFormBlock()->fill($integrationFixture)->save($integrationFixture);
        //Verification
        $this->checkSaveSuccessMessage();
        $this->ensureMatchingIntegrationData($integrationFixture);
    }

    /**
     * Edit Integration
     *
     * @ZephyrId MAGETWO-16759
     */
    public function testEditIntegration()
    {
        $this->markTestIncomplete('Obsolete selenium version on bamboo server');
        //Precondition
        $integrationFixture = Factory::getFixtureFactory()->getMagentoIntegrationIntegration();
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_TAB);
        $integrationFixture->persist();
        //Steps
        $editIntegrationPage = Factory::getPageFactory()->getAdminIntegrationEdit();
        $this->openByName($integrationFixture->getName());
        $editForm = $editIntegrationPage->getIntegrationFormBlock();
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_TAB);
        $editForm->update($integrationFixture)->save($integrationFixture);
        //Verification
        $this->checkSaveSuccessMessage();
        $this->ensureMatchingIntegrationData($integrationFixture);
    }

    /**
     * Navigate to the Integration page from Edit Integration page
     *
     * @ZephyrId MAGETWO-16823
     */
    public function testNavigation()
    {
        $this->markTestIncomplete('Obsolete selenium version on bamboo server');
        //Preconditions
        $integrationFixture = Factory::getFixtureFactory()->getMagentoIntegrationIntegration();
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_TAB);
        $integrationFixture->persist();
        //Steps
        $editIntegrationPage = Factory::getPageFactory()->getAdminIntegrationEdit();
        $this->openByName($integrationFixture->getName());
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
     * @ZephyrId MAGETWO-17305
     */
    public function testApiTabVerification()
    {
        $this->markTestIncomplete('Obsolete selenium version on bamboo server');
        //Data
        $integrationFixture = Factory::getFixtureFactory()->getMagentoIntegrationIntegration();
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_TAB);
        //Steps
        $newIntegrationPage = Factory::getPageFactory()->getAdminIntegrationNew();
        $newIntegrationPage->open();
        $newIntegrationPage->getIntegrationFormBlock()->fill($integrationFixture);
        $newIntegrationPage->getIntegrationFormBlock()->openApiTab();
        //Verification of JStree visibility by switching role access
        $this->assertTrue($newIntegrationPage->getApiTab()->isResourceVisible(), 'Resources tree should be visible.');
        $newIntegrationPage->getApiTab()->setRoleAccess('All');
        $this->assertFalse(
            $newIntegrationPage->getApiTab()->isResourceVisible(),
            'Resources tree should not be visible.'
        );
        $newIntegrationPage->getIntegrationFormBlock()->save();
        $this->checkSaveSuccessMessage();
        $this->openByName($integrationFixture->getName());
        $newIntegrationPage->getIntegrationFormBlock()->openApiTab();
        $this->assertEquals('All', $newIntegrationPage->getApiTab()->getRoleAccess());
        $this->assertFalse(
            $newIntegrationPage->getApiTab()->isResourceVisible(),
            'Resources tree should not be visible.'
        );
        $integrationFixture->switchData(IntegrationRepository::ALL_INTEGRATION_TABS);
        $newIntegrationPage->getIntegrationFormBlock()->fill($integrationFixture);
        $newIntegrationPage->getIntegrationFormBlock()->save($integrationFixture);
        //Verification of saved values of JStree
        $this->checkSaveSuccessMessage();
        $this->openByName($integrationFixture->getName());
        $newIntegrationPage->getIntegrationFormBlock()->openApiTab();
        $newIntegrationPage->getIntegrationFormBlock()->verify($integrationFixture);
    }

    /**
     * Check success message after integration save.
     */
    protected function checkSaveSuccessMessage()
    {
        $this->assertTrue(
            Factory::getPageFactory()->getAdminIntegration()->getMessageBlock()->assertSuccessMessage(),
            'Integration save success message was not found.'
        );
    }

    /**
     * Check integration data
     *
     * @param IntegrationFixture $integrationFixture
     */
    protected function ensureMatchingIntegrationData(IntegrationFixture $integrationFixture)
    {
        $editIntegrationPage = Factory::getPageFactory()->getAdminIntegrationEdit();
        $this->openByName($integrationFixture->getName());
        $editIntegrationPage->getIntegrationFormBlock()->verify($integrationFixture);
    }

    /**
     * Open existing integration page by integration name.
     *
     * @param string $integrationName
     */
    protected function openByName($integrationName)
    {
        $integrationGridPage = Factory::getPageFactory()->getAdminIntegration();
        $integrationGridPage->open();
        $integrationGridPage->getGridBlock()->searchAndOpen(array('name' => $integrationName));
    }
}
