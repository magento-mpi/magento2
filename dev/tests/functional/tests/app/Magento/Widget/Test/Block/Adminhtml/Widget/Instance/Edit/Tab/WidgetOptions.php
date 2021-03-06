<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\WidgetOptionsForm;
use Magento\Widget\Test\Fixture\Widget;
use Mtf\Client\Element;

/**
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
        foreach ($fields['widgetOptions']['value'] as $field) {
            $path = $this->path . ucfirst($field['type_id']);
            /** @var WidgetOptionsForm $widgetOptionsForm */
            $widgetOptionsForm = $this->blockFactory->create(
                $path,
                ['element' => $this->_rootElement->find($this->formSelector)]
            );
            $widgetOptionsForm->fillForm($field);
        }
        return $this;
    }
}
