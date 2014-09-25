<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CmsPageLink\Grid;

/**
 * Class CmsPageLink
 * Filling Widget Options that have cms page link type
 */
class CmsPageLink extends WidgetOptionsForm
{
    /**
     * Cms Page Link grid block
     *
     * @var string
     */
    // @codingStandardsIgnoreStart
    protected $cmsPageLinkGrid = '//*[@class="page-wrapper"]/ancestor::body//*[contains(@id, "responseCntoptions_fieldset")]';
    // @codingStandardsIgnoreEnd

    /**
     * Fill specified form data
     *
     * @param array $fields
     * @param Element $element
     */
    protected function _fill(array $fields, Element $element = null)
    {
        $context = ($element === null) ? $this->_rootElement : $element;
        foreach ($fields as $name => $field) {
            if ($name == 'entities') {
                $this->_rootElement->find($this->selectPage)->click();

                /** @var Grid $cmsPageLinkGrid */
                $cmsPageLinkGrid = $this->blockFactory->create(
                    'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CmsPageLink\Grid',
                    [
                        'element' => $this->_rootElement
                            ->find($this->cmsPageLinkGrid, Locator::SELECTOR_XPATH)
                    ]
                );
                $cmsPageLinkGrid->searchAndSelect(['title' => $field['value']['title']]);
            } elseif (!isset($field['value'])) {
                parent::_fill($field, $context);
            } else {
                $element = $this->getElement($context, $field);
                if ($this->mappingMode || ($element->isVisible() && !$element->isDisabled())) {
                    $element->setValue($field['value']);
                    $this->setFields[$name] = $field['value'];
                }
            }
        }
    }
}
