<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab;

use Mtf\Client\Element;
use Magento\Widget\Test\Fixture\Widget;
use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\AbstractWidgetOptionsForm;

/**
 * Class WidgetOptions
 * Widget options form
 */
class WidgetOptions extends Tab
{
    /**
     * Form selector
     *
     * @var string
     */
    protected $formSelector = '.fieldset-wide';

    /**
     * Path for widget options tab
     *
     * @var string
     */
    protected $path = 'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\\';

    /**
     * Fill Widget options form
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        foreach ($fields['widgetOptions']['value'] as $key => $field) {
            $path = $this->prepareClassPath($field['name']);
            unset($field['name']);
            /** @var AbstractWidgetOptionsForm $widgetOptionsForm */
            $widgetOptionsForm = $this->blockFactory->create(
                $path,
                [
                    'element' => $this->_rootElement->find($this->formSelector)
                ]
            );
            $widgetOptionsForm->fillForm($field);
        }
        return $this;
    }

    /**
     * Prepare class path
     *
     * @param string $widgetOptionsName
     * @return string
     */
    protected function prepareClassPath($widgetOptionsName)
    {
        if ($widgetOptionsName == 'recentlyComparedProducts' || $widgetOptionsName == 'recentlyViewedProducts') {
            return $this->path . 'RecentlyProducts';
        }

        return $this->path . ucfirst($widgetOptionsName);
    }
}
