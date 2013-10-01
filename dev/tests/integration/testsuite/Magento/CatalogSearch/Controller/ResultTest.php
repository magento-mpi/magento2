<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Controller;

class ResultTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoDataFixture Magento/CatalogSearch/_files/query.php
     * @magentoConfigFixture current_store general/locale/code de_DE
     */
    public function testIndexActionTranslation()
    {
        $this->markTestIncomplete('endTest() called too early, config fixture reseted');
        $this->getRequest()->setParam('q', 'query_text');
        $this->dispatch('catalogsearch/result');

        $responseBody = $this->getResponse()->getBody();

        $this->assertNotContains('Search:', $responseBody);
        $this->assertStringMatchesFormat('%aSuche%S:%a', $responseBody);

        $this->assertNotContains('Search entire store here...', $responseBody);
        $this->assertContains('Den gesamten Shop durchsuchen...', $responseBody);
    }
}
