<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Page;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as ParentGrid;

/**
 * Class Grid
 * Backend Cms Page grid
 */
class Grid extends ParentGrid
{
    /**
     * 'Add New' cms page button
     *
     * @var string
     */
    protected $addNewCmsPage = "//button[@id='add']";

    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $selectItem = 'td[class*=col-title]';

    /**
     * 'Preview' cms page link
     *
     * @var string
     */
    protected $previewCmsPage = "//a[contains(text(),'Preview')]";

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'title' => [
            'selector' => '#cmsPageGrid_filter_title'
        ],
    ];

    /**
     * Add new page
     *
     * @return void
     */
    public function addNewCmsPage()
    {
        $this->_rootElement->find($this->addNewCmsPage, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Preview page
     *
     * @return void
     */
    public function previewCmsPage()
    {
        $this->_rootElement->find($this->previewCmsPage, Locator::SELECTOR_XPATH)->click();
    }
}
