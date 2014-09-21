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
    protected $editLink = 'td[class*=col-title]';

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
     * Search item and open it on front
     *
     * @param array $filter
     * @throws \Exception
     * @return void
     */
    public function searchAndPreview(array $filter)
    {
        $this->search($filter);
        $rowItem = $this->_rootElement->find($this->rowItem, Locator::SELECTOR_CSS);
        if ($rowItem->isVisible()) {
            $rowItem->find($this->previewCmsPage, Locator::SELECTOR_XPATH)->click();
            $this->waitForElement();
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }
}
