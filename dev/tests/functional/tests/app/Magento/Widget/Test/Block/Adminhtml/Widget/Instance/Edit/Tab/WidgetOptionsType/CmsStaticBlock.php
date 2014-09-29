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
                /** @var Grid $cmsPageLinkGrid */
                $cmsPageLinkGrid = $this->blockFactory->create(
                    'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CmsStaticBlock\Grid',
                    [
                        'element' => $this->_rootElement
                            ->find($this->cmsStaticLinkGrid, Locator::SELECTOR_XPATH)
                    ]
                );
                // @codingStandardsIgnoreEnd
                $cmsPageLinkGrid->searchAndSelect(
                    [
                        'title' => $fields['entities']['value']['title'],
                        'identifier' => $fields['entities']['value']['identifier']
                    ]
                );
            } else {
                parent::_fill([$name => $field], $context);
            }
        }
    }
}
