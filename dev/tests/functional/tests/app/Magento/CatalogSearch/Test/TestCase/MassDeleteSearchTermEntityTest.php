<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\CatalogSearch\Test\Fixture\CatalogSearchQuery;
use Magento\CatalogSearch\Test\Page\Adminhtml\CatalogSearchIndex;

/**
 * Test Creation for MassDeleteSearchTermEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Search terms is created
 *
 * Steps:
 * 1. Go to backend as admin user
 * 2. Navigate to Marketing>SEO & Search>Search
 * 3. Select search terms created in preconditions
 * 4. Select delete from mass-action
 * 5. Submit form
 * 6. Perform all assertions
 *
 * @group Search_Terms_(MX)
 * @ZephyrId MAGETWO-26599
 */
class MassDeleteSearchTermEntityTest extends Injectable
{
    /**
     * Search term page
     *
     * @var CatalogSearchIndex
     */
    protected $indexPage;

    /**
     * Inject page
     *
     * @param CatalogSearchIndex $indexPage
     * @return void
     */
    public function __inject(CatalogSearchIndex $indexPage)
    {
        $this->indexPage = $indexPage;
    }

    /**
     * Run mass delete search term entity test
     *
     * @param string $searchTerms
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function test($searchTerms, FixtureFactory $fixtureFactory)
    {
        // Preconditions
        $result = [];
        $deleteSearchTerms = [];
        $searchTerms = array_map('trim', explode(',', $searchTerms));
        foreach ($searchTerms as $term) {
            list($fixture, $dataSet) = explode('::', $term);
            $term = $fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
            /** @var CatalogSearchQuery $term */
            $term->persist();
            $deleteSearchTerms[] = ['search_query' => $term->getQueryText()];
            $result['searchTerms'][] = $term;
        }

        // Steps
        $this->indexPage->open();
        $this->indexPage->getGrid()->delete($deleteSearchTerms);

        return $result;
    }
}
