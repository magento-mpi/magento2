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

namespace Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Selection;
use Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search\Grid;

/**
 * Class Option
 * Bundle options
 *
 * @package Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle
 */
class Option extends Block
{
    /**
     * Grid to assign products to bundle option
     *
     * @var Grid
     */
    private $searchGridBlock;

    /**
     * Added product row
     *
     * @var Selection
     */
    private $selectionBlock;

    /**
     * 'Add Products to Option' button
     *
     * @var string
     */
    private $addProducts;

    /**
     * Bundle option toggle
     */
    private $optionToggle;

    /**
     * Bundle option title
     *
     * @var string
     */
    private $title;

    /**
     * Bundle option type
     *
     * @var string
     */
    private $type;

    /**
     * Determine whether bundle options is require to fill
     *
     * @var string
     */
    private $required;

    /**
     * Counter for bundle options
     *
     * @var int
     */
    private $blockNumber = 0;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->optionToggle = '[data-target="#bundle_option_' . $this->blockNumber . '-content"]';
        $this->title = '#id_bundle_options_' . $this->blockNumber . '_title';
        $this->type = '#bundle_option_' . $this->blockNumber . '_type';
        $this->required = '#bundle_option_' . $this->blockNumber . ' #field-option-req';
        $this->addProducts = '#bundle_option_' . $this->blockNumber . '_add_button';
    }

    /**
     * Get grid for assigning products for bundle option
     *
     * @param Element $context
     * @return Grid
     */
    private function getSearchGridBlock(Element $context = null)
    {
        $element = $context ? $context : $this->_rootElement;
        $this->searchGridBlock = Factory::getBlockFactory()
            ->getMagentoBundleAdminhtmlCatalogProductEditTabBundleOptionSearchGrid(
                $element->find('[role=dialog][style*="display: block;"]')
            );
        return $this->searchGridBlock;
    }

    /**
     * Get product row assigned to bundle option
     *
     * @param int $rowNumber
     * @param Element $context
     * @return Selection
     */
    private function getSelectionBlock($rowNumber, Element $context = null)
    {
        $element = $context ? $context : $this->_rootElement;
        $this->selectionBlock = Factory::getBlockFactory()
            ->getMagentoBundleAdminhtmlCatalogProductEditTabBundleOptionSelection(
                $element->find('#bundle_option_' . $this->blockNumber . ' #bundle_selection_row_' . $rowNumber)
            );
        return $this->selectionBlock;
    }

    /**
     * Set block number
     *
     * @param int $blockNumber
     */
    public function setBlockNumber($blockNumber)
    {
        $this->blockNumber = $blockNumber;
        $this->_init();
    }

    /**
     * Expand block
     */
    public function expand()
    {
        if (!$this->_rootElement->find($this->title, Locator::SELECTOR_CSS)->isVisible()) {
            $this->_rootElement->find($this->optionToggle, Locator::SELECTOR_CSS)->click();
        }
    }

    /**
     * Fill bundle option
     *
     * @param array $fields
     * @param Element $context
     */
    public function fillBundleOption(array $fields, Element $context)
    {
        $rowNumber = 0;
        $this->fillOptionData($fields);
        foreach ($fields['assigned_products'] as $field) {
            if (is_array($field)) {
                $this->_rootElement->find($this->addProducts, Locator::SELECTOR_CSS)->click();
                $searchBlock = $this->getSearchGridBlock($context);
                $searchBlock->searchAndSelect($field['search_data']);
                $searchBlock->addProducts();
                $this->getSelectionBlock($rowNumber)->fillProductRow($field['data']);
                $rowNumber++;
            }
        }
    }

    /**
     * Update bundle option (now only general data, skipping assignments)
     *
     * @param array $fields
     */
    public function updateBundleOption(array $fields)
    {
        $this->fillOptionData($fields);
    }

    /**
     * Fill in general data to bundle option
     *
     * @param array $fields
     */
    private function fillOptionData(array $fields)
    {
        $this->_rootElement->find($this->title)->setValue($fields['title']['value']);
        $this->_rootElement->find($this->type, Locator::SELECTOR_CSS, 'select')->setValue($fields['type']['value']);
        $this->_rootElement->find($this->required, Locator::SELECTOR_CSS, 'checkbox')
            ->setValue($fields['required']['value']);
    }
}
