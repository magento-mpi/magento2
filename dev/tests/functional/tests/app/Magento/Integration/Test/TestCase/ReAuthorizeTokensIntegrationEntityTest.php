<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\TestCase;

use Magento\Integration\Test\Fixture\Integration;
use Magento\Integration\Test\Page\Adminhtml\IntegrationIndex;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Reauthorize tokens for the Integration Entity.
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create Integration
 * 2. Activate Integration
 *
 * Steps:
 * 1. Go to Integration page on backend
 * 2. Click on the "Reauthorize" link on the Integration grid
 * 3. Click on the "Reauthorize" button.
 * 4. Click Done
 * 5. Perform assertions
 *
 * @group Integrations_(PS)
 * @ZephyrId MAGETWO-29648
 */
class ReAuthorizeTokensIntegrationEntityTest extends Injectable
{
    /**
     * Integration grid page.
     *
     * @var IntegrationIndex
     */
    public $integrationIndex;

    /**
     * Factory for fixtures.
     *
     * @var FixtureFactory
     */
    public $fixtureFactory;

    /**
     * Injection data.
     *
     * @param FixtureFactory $fixtureFactory
     * @param IntegrationIndex $integrationIndex
     * @retun void
     */
    public function __inject(IntegrationIndex $integrationIndex, FixtureFactory $fixtureFactory)
    {
        $this->integrationIndex = $integrationIndex;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Test for Reauthorize tokens for the Integration Entity.
     *
     * @param Integration $integration
     * @return array
     */
    public function test(Integration $integration)
    {
        $this->markTestIncomplete('MAGETWO-26850');
        // Precondition
        $integration->persist();
        $filter = ['name' => $integration->getName()];
        $this->integrationIndex->open();
        $this->integrationIndex->getIntegrationGrid()->searchAndActivate($filter);
        $this->integrationIndex->getIntegrationGrid()->getResourcesPopup()->clickAllowButton();
        $tokens = $this->integrationIndex->getIntegrationGrid()->getTokensPopup()->getData();
        $this->integrationIndex->getIntegrationGrid()->getTokensPopup()->clickDoneButton();
        $integration = $this->fixtureFactory->createByCode(
            'integration',
            ['data' => array_merge($integration->getData(), $tokens)]
        );

        // Steps
        $this->integrationIndex->getIntegrationGrid()->searchAndReauthorize($filter);
        $this->integrationIndex->getIntegrationGrid()->getResourcesPopup()->clickReauthorizeButton();
        $this->integrationIndex->getIntegrationGrid()->getTokensPopup()->clickDoneButton();

        return ['integration' => $integration];
    }
}
