<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
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
 * @package Magento\CatalogSearch\Test\Page
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
     * @var \Magento\CatalogSearch\Test\Block\Form\Advanced
     */
    protected $searchForm;

    /**
     * Form wrapper selector
     *
     * @var string
     */
    private $formWrapperSelector = '.form.search.advanced';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
        $this->searchForm = Factory::getBlockFactory()->getMagentoCatalogSearchFormAdvanced(
            $this->_browser->find($this->formWrapperSelector, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get search block form
     *
     * @return \Magento\CatalogSearch\Test\Block\Form\Advanced
     */
    public function getSearchForm()
    {
        return $this->searchForm;
    }
}
