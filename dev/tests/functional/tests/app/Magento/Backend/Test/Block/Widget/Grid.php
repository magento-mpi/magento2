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

namespace Magento\Backend\Test\Block\Widget;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Abstract class Grid
 * Basic grid actions
 *
 * @package Magento\Backend\Test\Block\Widget
 */
abstract class Grid extends Block
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = array();

    /**
     * Locator value for 'Search' button
     *
     * @var string
     */
    protected $searchButton;

    /**
     * Locator value for 'Reset' button
     *
     * @var string
     */
    protected $resetButton;

    /**
     * The first row in grid. For this moment we suggest that we should strictly define what we are going to search
     *
     * @var string
     */
    protected $rowItem;

    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem;

    /**
     * The body element of the page
     *
     * @var Template
     */
    protected $_templateBlock;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->searchButton = '[title=Search][class*=action]';
        $this->resetButton = '[title="Reset Filter"][class*=action]';
        $this->rowItem = 'tbody tr';
        $this->selectItem = 'tbody tr .col-select';
        $this->_templateBlock = Factory::getBlockFactory()->getMagentoBackendTemplate(
            $this->_rootElement->find('./ancestor::body', \Mtf\Client\Element\Locator::SELECTOR_XPATH));
    }

    /**
     * Prepare data to perform search, fill in search filter
     *
     * @param array $filters
     * @throws \Exception
     */
    private function _prepareForSearch(array $filters)
    {
        foreach ($filters as $key => $value) {
            if (isset($this->filters[$key])) {
                $selector = $this->filters[$key]['selector'];
                $strategy = isset($this->filters[$key]['strategy'])
                    ? $this->filters[$key]['strategy']
                    : Locator::SELECTOR_CSS;
                $typifiedElement = isset($this->filters[$key]['input'])
                    ? $this->filters[$key]['input']
                    : null;
                $this->_rootElement->find($selector, $strategy, $typifiedElement)->setValue($value);
            } else {
                throw new \Exception('Such column is absent in the grid or not described yet.');
            }
        }
    }

    /**
     * Search item via grid filter
     *
     * @param array $filter
     */
    public function search(array $filter)
    {
        $this->resetFilter();
        sleep(2);
        $this->_prepareForSearch($filter);
        sleep(2);
        $this->_rootElement->find($this->searchButton, Locator::SELECTOR_CSS)->click();
        sleep(2);
        $this->_templateBlock->waitLoader();
    }

    /**
     * Search item and open it
     *
     * @param array $filter
     * @throws \Exception
     */
    public function searchAndOpen(array $filter)
    {
        $this->search($filter);
        $rowItem = $this->_rootElement->find($this->rowItem, Locator::SELECTOR_CSS);
        if ($rowItem->isVisible()) {
            $rowItem->find('//td[@data-column="action"]//a', Locator::SELECTOR_XPATH)->click();
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }

    /**
     * Search for item and select it
     *
     * @param array $filter
     * @throws \Exception
     */
    public function searchAndSelect(array $filter)
    {
        $this->search($filter);
        $selectItem = $this->_rootElement->find($this->selectItem);
        if ($selectItem->isVisible()) {
            $selectItem->click();
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }

    /**
     * Press 'Reset' button
     */
    public function resetFilter()
    {
        $this->_rootElement->find($this->resetButton, Locator::SELECTOR_CSS)->click();
        $this->_templateBlock->waitLoader();
    }

    public function deleteAll()
    {
        //
    }

    /**
     * Check if specific row exists in grid
     *
     * @param array $filter
     * @return bool
     */
    public function isRowVisible(array $filter)
    {
        $this->search($filter);
        $location = '//div[@class="grid"]//tr[';
        $rows = array();
        foreach ($filter as $value) {
            $rows[] = 'td[text()[normalize-space()="' . $value . '"]]';
        }
        $location = $location . implode(' and ', $rows) . ']';
        return $this->_rootElement->find($location, Locator::SELECTOR_XPATH)->isVisible();
    }
}
