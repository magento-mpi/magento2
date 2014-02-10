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
    protected $searchButton = '[title=Search][class*=action]';

    /**
     * Locator value for 'Reset' button
     *
     * @var string
     */
    protected $resetButton = '[title="Reset Filter"][class*=action]';

    /**
     * The first row in grid. For this moment we suggest that we should strictly define what we are going to search
     *
     * @var string
     */
    protected $rowItem = 'tbody tr';

    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-action] a';

    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .col-select';

    /**
     * 'Select All' link
     *
     * @var string
     */
    protected $selectAll = '.massaction a[onclick*=".selectAll()"]';

    /**
     * Massaction dropdown
     *
     * @var string
     */
    protected $massactionSelect = '[id*=massaction-select]';

    /**
     * Massaction 'Submit' button
     *
     * @var string
     */
    protected $massactionSubmit = '[id*=massaction-form] button';

    /**
     * Backend abstract block
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Selector of element to wait for. If set by child will wait for element after action
     *
     * @var string
     */
    protected $waitForSelector;

    /**
     * Locator type of waitForSelector
     *
     * @var Locator
     */
    protected $waitForSelectorType = Locator::SELECTOR_CSS;

    /**
     * Wait for should be for visibility or not?
     *
     * @var boolean
     */
    protected $waitForSelectorVisible = true;

    /**
     * Get backend abstract block
     *
     * @return \Magento\Backend\Test\Block\Template
     */
    protected function getTemplateBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendTemplate(
            $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)
        );
    }

    /**
     * Prepare data to perform search, fill in search filter
     *
     * @param array $filters
     * @throws \Exception
     */
    private function prepareForSearch(array $filters)
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
        $this->prepareForSearch($filter);
        $this->_rootElement->find($this->searchButton, Locator::SELECTOR_CSS)->click();
        $this->getTemplateBlock()->waitLoader();
        $this->reinitRootElement();
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
            $rowItem->find($this->editLink, Locator::SELECTOR_CSS)->click();
            $this->waitForElement();
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }


    /**
     * Method that waits for the configured selector using class attributes.
     */
    protected function waitForElement()
    {
        if (!empty($this->waitForSelector)) {
            if ($this->waitForSelectorVisible) {
                $this->getTemplateBlock()->waitForElementVisible($this->waitForSelector, $this->waitForSelectorType);
            } else {
                $this->getTemplateBlock()->waitForElementNotVisible($this->waitForSelector, $this->waitForSelectorType);
            }
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
        $this->getTemplateBlock()->waitLoader();
        $this->reinitRootElement();
    }

    /**
     * Perform selected massaction over checked items
     *
     * @param $actionType
     * @param array $items
     * @param bool $acceptAlert
     */
    protected function massaction($actionType, array $items = array(), $acceptAlert = false)
    {
        if ($items) {
            foreach ($items as $item) {
                $this->searchAndSelect($item);
            }
        } else {
            $this->_rootElement->find($this->selectAll, Locator::SELECTOR_CSS)->click();
        }
        $this->_rootElement->find($this->massactionSelect, Locator::SELECTOR_CSS, 'select')->setValue($actionType);
        $this->_rootElement->find($this->massactionSubmit, Locator::SELECTOR_CSS)->click();
        if ($acceptAlert) {
            $this->_rootElement->acceptAlert();
        }
    }

    /**
     * Delete selected items in grid
     *
     * @param array $items
     */
    public function delete($items = array())
    {
        $this->massaction('Delete', $items, true);
    }

    /**
     * Update attributes for selected items
     *
     * @param array $items
     */
    public function updateAttributes(array $items = array())
    {
        $this->massaction('Update Attributes', $items);
    }

    /**
     * Obtain specific row in grid
     *
     * @param array $filter
     * @param bool $isSearchable
     * @return Element
     */
    protected function getRow(array $filter, $isSearchable = true)
    {
        if ($isSearchable) {
            $this->search($filter);
        }
        $location = '//div[@class="grid"]//tr[';
        $rows = array();
        foreach ($filter as $value) {
            $rows[] = 'td[text()[normalize-space()="' . $value . '"]]';
        }
        $location = $location . implode(' and ', $rows) . ']';
        return $this->_rootElement->find($location, Locator::SELECTOR_XPATH);
    }

    /**
     * Check if specific row exists in grid
     *
     * @param array $filter
     * @param bool $isSearchable
     * @return bool
     */
    public function isRowVisible(array $filter, $isSearchable = true)
    {
        return $this->getRow($filter, $isSearchable)->isVisible();
    }
}
