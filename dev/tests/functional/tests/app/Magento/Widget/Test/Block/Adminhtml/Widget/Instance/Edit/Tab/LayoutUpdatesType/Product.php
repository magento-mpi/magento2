<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType\Product\Grid;

/**
 * Class Product
 * Filling Product type layout
 */
class Product extends LayoutForm
{
    /**
     * Product grid block
     *
     * @var string
     */
    protected $productGrid = '.chooser_container';

    /**
     * Filling layout form
     *
     * @param array $widgetOptionsFields
     * @param Element $element
     * @return void
     */
    public function fillForm(array $widgetOptionsFields, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($widgetOptionsFields);
        if (isset($mapping['entities'])) {
            $entities = $mapping['entities'];
            unset($mapping['entities']);
        }
        $this->_fill($mapping, $element);

        if (!empty($entities)) {
            $this->_rootElement->find($this->chooser)->click();
            $this->getTemplateBlock()->waitLoader();

            /** @var Grid $productGrid */
            $productGrid = $this->blockFactory->create(
                'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType\Product\Grid',
                [
                    'element' => $this->_rootElement
                        ->find($this->productGrid, Locator::SELECTOR_CSS)
                ]
            );
            $productGrid->searchAndSelect(['name' => $entities['value']['name']]);
            $this->getTemplateBlock()->waitLoader();
        }
    }
}
