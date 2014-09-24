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
     * Locator value for 'Search' button
     *
     * @var string
     */
    protected $searchButton = '.action.primary.action-apply';

    /**
     * Locator value for 'Reset' button
     *
     * @var string
     */
    protected $resetButton = '.action.secondary.action-reset';

    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'td[data-part="body.row.cell"]';

    /**
     * Filter button
     *
     * @var string
     */
    protected $filterButton= '.action.filters-toggle';

    /**
     * Active class
     *
     * @var string
     */
    protected $active = '.active';

    /**
     * 'Preview' cms page link
     *
     * @var string
     */
    protected $previewCmsPage = "//a[contains(text(),'Preview')]";

    /**
     * Filters array mapping
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'title' => [
            'selector' => '#title'
        ],
    ];

    /**
     * Search item and open it
     *
     * @param array $filter
     * @throws \Exception
     */
    public function searchAndOpen(array $filter)
    {
        if (!$this->_rootElement->find($this->filterButton . $this->active)->isVisible()) {
            $this->_rootElement->find($this->filterButton)->click();
        }
        parent::searchAndOpen($filter);
    }

    /**
     * Search item and open it on front
     *
     * @param array $filter
     * @throws \Exception
     * @return void
     */
    public function searchAndPreview(array $filter)
    {
        if (!$this->_rootElement->find($this->filterButton . $this->active)->isVisible()) {
            $this->_rootElement->find($this->filterButton)->click();
        }
        $this->search($filter);
        $rowItem = $this->_rootElement->find($this->rowItem);
        if ($rowItem->isVisible()) {
            $rowItem->find($this->previewCmsPage, Locator::SELECTOR_XPATH)->click();
            $this->waitForElement();
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }
}
