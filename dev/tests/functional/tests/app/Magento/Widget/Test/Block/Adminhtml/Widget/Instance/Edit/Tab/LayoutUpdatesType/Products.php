<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType;

use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType\Product\Grid;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Filling Product type layout
 */
class Products extends LayoutForm
{
    /**
     * Product grid block
     *
     * @var string
     */
    protected $productGrid = '//*[@class="chooser_container"]';

    /**
     * Filling layout form
     *
     * @param array $widgetOptionsFields
     * @param Element $element
     * @return void
     */
    public function fillForm(array $widgetOptionsFields, Element $element = null)
    {
        $mapping = $this->dataMapping($widgetOptionsFields);
        $fields = array_diff_key($mapping, ['entities' => '']);
        foreach ($fields as $key => $values) {
            $this->_fill([$key => $values], $this->_rootElement);
            $this->getTemplateBlock()->waitLoader();
            $this->reinitRootElement();
        }
        if (isset($mapping['entities'])) {
            $this->selectEntityInGrid($mapping['entities']);
        }
    }

    /**
     * Select entity in grid on layout tab
     *
     * @param array $entities
     * @return void
     */
    protected function selectEntityInGrid(array $entities)
    {
        $this->_rootElement->find($this->chooser, Locator::SELECTOR_XPATH)->click();
        $this->getTemplateBlock()->waitLoader();

        /** @var Grid $productGrid */
        $productGrid = $this->blockFactory->create(
            'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType\Product\Grid',
            [
                'element' => $this->_rootElement
                    ->find($this->productGrid, Locator::SELECTOR_XPATH)
            ]
        );
        $productGrid->searchAndSelect(['name' => $entities['value']['name']]);
        $this->getTemplateBlock()->waitLoader();
        $this->_rootElement->find($this->apply, Locator::SELECTOR_XPATH)->click();
    }
}
