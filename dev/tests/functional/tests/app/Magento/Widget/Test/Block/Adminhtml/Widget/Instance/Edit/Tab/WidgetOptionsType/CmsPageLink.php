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
            $this->_rootElement->find($this->selectPage)->click();

            /** @var Grid $cmsPageLinkGrid */
            $cmsPageLinkGrid = $this->blockFactory->create(
                'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CmsPageLink\Grid',
                [
                    'element' => $this->_rootElement
                        ->find($this->cmsPageLinkGrid, Locator::SELECTOR_XPATH)
                ]
            );
            $cmsPageLinkGrid->searchAndSelect(['title' => $entities['value'][0]->getTitle()]);
        }
    }
}
