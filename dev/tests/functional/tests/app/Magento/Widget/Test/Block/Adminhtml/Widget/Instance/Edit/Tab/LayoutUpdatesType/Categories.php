<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType;

use Mtf\Client\Element;

/**
 * Class Categories
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
        if (isset($mapping['entities'])) {
            $entities = $mapping['entities'];
            unset($mapping['entities']);
        }
        $this->_fill($mapping, $element);

        if (!empty($entities)) {
            $this->_rootElement->find($this->chooser)->click();
            $this->getTemplateBlock()->waitLoader();
            $parentPath = $entities['value']['path'];
            $entities['value'] = $parentPath . '/' . $entities['value']['name'];
            $this->_fill([$entities], $element);
            $this->getTemplateBlock()->waitLoader();
        }
    }
}
