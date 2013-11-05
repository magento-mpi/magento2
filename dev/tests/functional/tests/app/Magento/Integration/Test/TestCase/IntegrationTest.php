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
 * @TODO Tests in current test case should be replaced with multiple independent tests according to test plan
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
        $editIntegrationPage->openByName($integrationFixture->getName());
        $integrationFixture->switchData(IntegrationRepository::INTEGRATION_OAUTH);
        $editForm = $editIntegrationPage->getIntegrationFormBlock();
        $editForm->update($integrationFixture)->save($integrationFixture);
        $this->_checkSaveSuccessMessage($integrationFixture);
        $this->_ensureMatchingIntegrationExists($integrationFixture);
    }

    /**
     * @param IntegrationFixture $integrationFixture injectable
     */
    public function testHandler(IntegrationFixture $integrationFixture)
    {
        /** Create integration using fixtures mechanisms */
        $integrationFixture->persist();
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

    protected function _ensureMatchingIntegrationExists(IntegrationFixture $integrationFixture)
    {
        $editIntegrationPage = Factory::getPageFactory()->getAdminIntegrationEdit();
        $editIntegrationPage->openByName($integrationFixture->getName());
        /** TODO: verify() method seems to be broken and should be fixed */
        // $editIntegrationPage->getIntegrationFormBlock()->verify($integrationFixture);
    }
}
