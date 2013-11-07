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

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Block\Search;
use Magento\Page\Test\Block\Html\Title;
use Magento\Page\Test\Block\Html\Topmenu;
use Magento\Page\Test\Block\Links;

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
     * Top menu navigation block
     *
     * @var Topmenu
     */
    private $topmenuBlock;

    /**
     * Page title block
     *
     * @var Title
     */
    private $titleBlock;

    /**
     * Page Top Links block
     *
     * @var Links
     */
    private $topLinks;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'];
        $this->searchBlock = Factory::getBlockFactory()->getMagentoCatalogSearch(
            $this->_browser->find('search_mini_form', Locator::SELECTOR_ID)
        );
        $this->topmenuBlock = Factory::getBlockFactory()->getMagentoPageHtmlTopmenu(
            $this->_browser->find('[role=navigation]', Locator::SELECTOR_CSS)
        );
        $this->titleBlock = Factory::getBlockFactory()->getMagentoPageHtmlTitle(
            $this->_browser->find('.page.title', Locator::SELECTOR_CSS)
        );
        $this->topLinks = Factory::getBlockFactory()->getMagentoPageLinks(
            $this->_browser->find('.header .content .links', Locator::SELECTOR_CSS)
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

    /**
     * Get Top Links block
     *
     * @return Links
     */
    public function getTopLinks()
    {
        return $this->topLinks;
    }
}
