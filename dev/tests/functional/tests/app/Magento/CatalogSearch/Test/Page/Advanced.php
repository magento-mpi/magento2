<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Advanced search page
 *
 */
class Advanced extends Page
{
    /**
     * URL for search advanced page
     */
    const MCA = 'catalogsearch/advanced';

    /**
     * Advanced search form
     *
     * @var string
     */
    protected $searchForm = '.form.search.advanced';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get search block form
     *
     * @return \Magento\CatalogSearch\Test\Block\Form\Advanced
     */
    public function getSearchForm()
    {
        return Factory::getBlockFactory()->getMagentoCatalogSearchFormAdvanced(
            $this->_browser->find($this->searchForm, Locator::SELECTOR_CSS)
        );
    }
}
