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
class CatalogProductLink extends AbstractWidgetOptionsForm
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
        $this->_fill(array_diff_key($mapping, ['entities' => '']), $element);
        if (isset($mapping['entities'])) {
            $this->selectEntityInGrid($mapping['entities']);
        }
    }

    /**
     * Select entity in grid on widget options tab
     *
     * @param array $entities
     * @return void
     */
    protected function selectEntityInGrid(array $entities)
    {
        $this->_rootElement->find($this->selectBlock)->click();

        /** @var Grid $catalogProductLinkGrid */
        $catalogProductLinkGrid = $this->blockFactory->create(
            'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CatalogProductLink\Grid',
            [
                'element' => $this->_rootElement
                    ->find($this->catalogProductLinkGrid, Locator::SELECTOR_XPATH)
            ]
        );
        $catalogProductLinkGrid->searchAndSelect(['name' => $entities['value'][0]->getName()]);
    }
}
