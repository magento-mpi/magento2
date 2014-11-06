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

/**
 * Filling Categories type layout
 */
class Categories extends LayoutForm
{
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
        $this->_fill(array_diff_key($mapping, ['entities' => '']), $element);
        if (isset($mapping['entities'])) {
            $this->selectCategory($mapping['entities'], $element);
        }
    }

    /**
     * Select category on layout tab
     *
     * @param array $entities
     * @param Element $element
     * @return void
     */
    protected function selectCategory(array $entities, Element $element)
    {
        $this->_rootElement->find($this->chooser)->click();
        $this->getTemplateBlock()->waitLoader();
        $parentPath = $entities['value']['path'];
        $entities['value'] = $parentPath . '/' . $entities['value']['name'];
        $this->_fill([$entities], $element);
        $this->getTemplateBlock()->waitLoader();
        $this->_rootElement->find($this->apply, Locator::SELECTOR_XPATH)->click();
    }
}
