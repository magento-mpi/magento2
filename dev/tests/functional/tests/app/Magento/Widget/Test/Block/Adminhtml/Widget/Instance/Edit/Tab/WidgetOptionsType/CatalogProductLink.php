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
        if (isset($mapping['entities'])) {
            $entities = $mapping['entities'];
            unset($mapping['entities']);
        }
        $this->_fill($mapping, $element);
        if (!empty($entities)) {
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
            $catalogProductLinkGrid->searchAndSelect(['name' => $entities['value']['name']]);
        }
    }
}
