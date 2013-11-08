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
 * Example of integration-related pages, blocks and fixtures usage.
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
     * Creating new Integration with different authentication types
     *
     * @ZephyrId MAGETWO-16694
     *
     * @param IntegrationFixture $integrationFixture injectable
     */
    public function testBasicFlow(IntegrationFixture $integrationFixture)
    {
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_MANUAL);

        /** Create integration using UI */
        $newIntegrationPage = Factory::getPageFactory()->getAdminIntegrationNew();
        $newIntegrationPage->open();
        $newIntegrationPage->getIntegrationFormBlock()->fill($integrationFixture)->save($integrationFixture);
        $this->_checkSaveSuccessMessage($integrationFixture);
        $this->_ensureMatchingIntegrationExists($integrationFixture);

        /** Update integration data */
        $editIntegrationPage = Factory::getPageFactory()->getAdminIntegrationEdit();
        $this->_openByName($integrationFixture->getName());
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_OAUTH);
        $editForm = $editIntegrationPage->getIntegrationFormBlock();
        $editForm->update($integrationFixture)->save($integrationFixture);
        $this->_checkSaveSuccessMessage($integrationFixture);
        $this->_ensureMatchingIntegrationExists($integrationFixture);
    }

    /**
     * Edit Integration
     *
     * @ZephyrId MAGETWO-16759
     *
     * @param IntegrationFixture $integrationFixture injectable
     */
    public function testEditIntegration(IntegrationFixture $integrationFixture)
    {
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_OAUTH);
        $integrationFixture->persist();
        $editIntegrationPage = Factory::getPageFactory()->getAdminIntegrationEdit();
        $this->_openByName($integrationFixture->getName());
        $editForm = $editIntegrationPage->getIntegrationFormBlock();
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_MANUAL);
        $editForm->update($integrationFixture)->save($integrationFixture);
        $this->_checkSaveSuccessMessage($integrationFixture);
        $this->_ensureMatchingIntegrationExists($integrationFixture);
    }

    /**
     * Search Integration in the Integration's grid
     *
     * @ZephyrId MAGETWO-16721
     *
     * @param IntegrationFixture $integrationFixture injectable
     */
    public function testSearch(IntegrationFixture $integrationFixture)
    {
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_OAUTH);
        $integrationFixture->persist();
        Factory::getPageFactory()->getAdminIntegrationEdit();
        $this->_openByName($integrationFixture->getName());
    }

    /**
     * Reset data in the New Integration form
     *
     * @ZephyrId MAGETWO-16722
     *
     * @param IntegrationFixture $integrationFixture injectable
     */
    public function testResetData(IntegrationFixture $integrationFixture)
    {
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_OAUTH);
        $originalFixture = clone $integrationFixture;
        $integrationFixture->persist();
        $editIntegrationPage = Factory::getPageFactory()->getAdminIntegrationEdit();
        $this->_openByName($integrationFixture->getName());
        $editForm = $editIntegrationPage->getIntegrationFormBlock();
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_MANUAL);
        $editForm->update($integrationFixture)->reset($integrationFixture);

        $editForm = $editIntegrationPage->getIntegrationFormBlock();
        $editForm->reinitRootElement();
        $editForm->verify($originalFixture);
    }

    /**
     * Navigate to the Integration page from Edit Integration page
     *
     * @ZephyrId MAGETWO-16723
     *
     * @param IntegrationFixture $integrationFixture injectable
     */
    public function testNavigation(IntegrationFixture $integrationFixture)
    {
        /** Create integration using fixtures mechanisms */
        $integrationFixture->persist();
        $editIntegrationPage = Factory::getPageFactory()->getAdminIntegrationEdit();

        $this->_openByName($integrationFixture->getName());
        $editIntegrationPage->getIntegrationFormBlock()->back();

        $this->_ensureMatchingIntegrationExists($integrationFixture);
    }


    /**
     * Check success message after integration save.
     *
     * @param IntegrationFixture $fixture
     */
    protected function _checkSaveSuccessMessage($fixture)
    {
        /** TODO: Message validation functionality can be added to message block */
        $this->assertTrue(
            Factory::getPageFactory()->getAdminIntegration()->getMessageBlock()->waitForSuccessMessage($fixture),
            'Integration save success message was not found.'
        );
    }

    /**
     * Check if integration exists
     *
     * @param IntegrationFixture $integrationFixture
     */
    protected function _ensureMatchingIntegrationExists(IntegrationFixture $integrationFixture)
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
