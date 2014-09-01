<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\System\Store;

use Mtf\Client\Element\Locator;
use Magento\Store\Test\Fixture\StoreGroup;
use Magento\Store\Test\Fixture\Website;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class StoreGrid
 * Adminhtml Store View management grid
 */
class StoreGrid extends GridInterface
{
    /**
     * Locator value for opening needed row
     *
     * @var string
     */
    protected $editLink = 'td[data-column="store_title"] > a';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'store_title' => [
            'selector' => '#storeGrid_filter_store_title',
        ],
        'group_title' => [
            'selector' => '#storeGrid_filter_group_title'
        ],
        'website_title' => [
            'selector' => '#storeGrid_filter_website_title'
        ]
    ];

    /**
     * Store title format for XPATH
     *
     * @var string
     */
    protected $titleFormat = '//td[a[.="%s"]]';

    /**
     * Store name link selector
     *
     * @var string
     */
    protected $storeName = '//a[.="%s"]';

    /**
     * Check if store exists
     *
     * @param string $title
     * @return bool
     */
    public function isStoreExists($title)
    {
        $element = $this->_rootElement->find(sprintf($this->titleFormat, $title), Locator::SELECTOR_XPATH);
        return $element->isVisible();
    }

    /**
     * Click to appropriate store in Store grid for edit
     *
     * @param string $name
     * @return void
     */
    public function editStore($name)
    {
        $this->_rootElement->find(sprintf($this->storeName, $name), Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Check if website exists
     *
     * @param Website $website
     * @return bool
     */
    public function isWebsiteExists($website)
    {
        return $this->_rootElement->find(sprintf($this->titleFormat, $website->getName()), Locator::SELECTOR_XPATH)
            ->isVisible();
    }

    /**
     * Search and open appropriate Website
     *
     * @param Website $website
     * @return void
     */
    public function searchAndOpenWebsite(Website $website)
    {
        $websiteName = $website->getName();
        $this->search(['website_title' => $websiteName]);
        $this->_rootElement->find(sprintf($this->storeName, $websiteName), Locator::SELECTOR_XPATH)->click();
    }
}
