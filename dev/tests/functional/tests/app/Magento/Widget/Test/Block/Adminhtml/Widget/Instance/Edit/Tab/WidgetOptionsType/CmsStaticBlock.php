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
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType\Product\Grid;

/**
 * Class CmsStaticBlock
 * Filling Widget Options that have cms static block type
 */
class CmsStaticBlock extends WidgetOptionsForm
{
    /**
     * Select page button
     *
     * @var string
     */
    protected $selectBlock = '.action-.scalable.btn-chooser';

    /**
     * Cms Page Link grid block
     *
     * @var string
     */
    // @codingStandardsIgnoreStart
    protected $cmsStaticLinkGrid = '//*[@class="page-wrapper"]/ancestor::body//*[contains(@id, "responseCntoptions_fieldset")]';
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
            /** @var Grid $cmsPageLinkGrid */
            $cmsPageLinkGrid = $this->blockFactory->create(
                'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CmsStaticBlock\Grid',
                [
                    'element' => $this->_rootElement->find($this->cmsStaticLinkGrid, Locator::SELECTOR_XPATH)
                ]
            );
            $cmsPageLinkGrid->searchAndSelect(
                [
                    'title' => $entities['value']['title'],
                    'identifier' => $entities['value']['identifier']
                ]
            );
        }

    }
}
