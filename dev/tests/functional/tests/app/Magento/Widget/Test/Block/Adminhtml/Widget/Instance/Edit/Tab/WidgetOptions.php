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
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\WidgetOptionsForm;

/**
 * Class LayoutUpdates
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
     * Fill Widget options form
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        foreach ($fields['widgetOptions']['value'] as $key => $field) {
            $dataBlock = $this->optionNameConvert($field['name']);
            unset($field['name']);
            $path = '\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\\';
            /** @var WidgetOptionsForm $widgetOptionsForm */
            $widgetOptionsForm = $this->blockFactory->create(
                'Magento\\' . $dataBlock['module'] . $path . $dataBlock['name'],
                [
                    'element' => $this->_rootElement->find($this->formSelector)
                ]
            );
            $widgetOptionsForm->fillForm($field);
        }
        return $this;
    }

    /**
     * Prepare class name
     *
     * @param string $widgetOptionsName
     * @return array
     */
    protected function optionNameConvert($widgetOptionsName)
    {
        if ($widgetOptionsName == 'recentlyComparedProducts' || $widgetOptionsName == 'recentlyViewedProducts') {
            return ['module' => 'Widget', 'name' => 'RecentlyComparedProducts'];
        }

        return ['module' => 'Widget', 'name' => ucfirst($widgetOptionsName)];
    }
}
