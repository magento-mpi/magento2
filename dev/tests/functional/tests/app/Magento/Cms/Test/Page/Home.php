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

namespace Magento\Cms\Test\Page;

use Magento\Core\Test\Block\Title;
use Magento\Page\Test\Block\Html\Topmenu;
use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Block\Search;

/**
 * Class Home
 * Home page for frontend
 *
 * @package Magento\Mage\Test\Page
 */
class Home extends Page
{
    /**
     * URL for home page
     */
    const MCA = 'cms/index/index';

    /**
     * Search block
     *
     * @var Search
     */
    private $searchBlock;

    /**
     * @var Topmenu
     */
    private $topmenuBlock;

    /**
     * @var Title
     */
    private $titleBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'];
        $this->searchBlock = Factory::getBlockFactory()->getMagentoCatalogSearch(
            $this->_browser->find('search_mini_form', Locator::SELECTOR_ID));
        $this->topmenuBlock = Factory::getBlockFactory()->getMagentoPageHtmlTopmenu(
            $this->_browser->find('.navigation', Locator::SELECTOR_CSS)
        );
        $this->titleBlock = Factory::getBlockFactory()->getMagentoCoreTitle(
            $this->_browser->find('h1.title span', Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get the search block
     *
     * @return Search
     */
    public function getSearchBlock()
    {
        return $this->searchBlock;
    }

    /**
     * Get category title block
     *
     * @return Topmenu
     */
    public function getTopmenu()
    {
        return $this->topmenuBlock;
    }

    /**
     * Get title block
     *
     * @return Title
     */
    public function getTitleBlock()
    {
        return $this->titleBlock;
    }
}
