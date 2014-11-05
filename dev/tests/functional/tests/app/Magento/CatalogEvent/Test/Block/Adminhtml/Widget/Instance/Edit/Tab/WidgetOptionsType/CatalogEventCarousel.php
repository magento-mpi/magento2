<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType;

use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\WidgetOptionsForm;
use Mtf\Client\Element;

/**
 * Filling Widget Options that have catalog event carousel type
 */
class CatalogEventCarousel extends WidgetOptionsForm
{
    /**
     * Filling widget options form
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
    }
}
