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
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CatalogProductLink\Grid;

/**
 * Class CatalogProductLink
 * Filling Widget Options that have catalog product link type
 */
class CatalogProductLink extends WidgetOptionsForm
{
    /**
     * Select page button
     *
     * @var string
     */
    protected $selectBlock = '.action-.scalable.btn-chooser';

    /**
     * Catalog Product Link grid block
     *
     * @var string
     */
    // @codingStandardsIgnoreStart
    protected $catalogProductLinkGrid = '//*[@class="page-wrapper"]/ancestor::body//*[contains(@id, "options_fieldset")]//div[contains(@class, "main-col")]';
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
                $this->_rootElement->find($this->selectBlock)->click();

                // @codingStandardsIgnoreStart
                /** @var Grid $catalogProductLinkGrid */
                $catalogProductLinkGrid = $this->blockFactory->create(
                    'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CatalogProductLink\Grid',
                    [
                        'element' => $this->_rootElement
                            ->find($this->catalogProductLinkGrid, Locator::SELECTOR_XPATH)
                    ]
                );
                // @codingStandardsIgnoreEnd
                $catalogProductLinkGrid->searchAndSelect(['name' => $field['value']['name']]);
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
