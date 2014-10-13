<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType;

use Mtf\Client\Element;
use Magento\Banner\Test\Block\Adminhtml\Banner\Grid;
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\WidgetOptionsForm;

/**
 * Class BannerRotator
 * Filling Widget Options that have banner rotator type
 */
class BannerRotator extends WidgetOptionsForm
{
    /**
     * Banner Rotator grid block
     *
     * @var string
     */
    protected $bannerRotatorGrid = '#bannerGrid';

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
            /** @var Grid $bannerRotatorGrid */
            $bannerRotatorGrid = $this->blockFactory->create(
                'Magento\Banner\Test\Block\Adminhtml\Banner\Grid',
                [
                    'element' => $this->_rootElement
                        ->find($this->bannerRotatorGrid)
                ]
            );
            $bannerRotatorGrid->searchAndSelect(['banner' => $entities['value'][0]->getName()]);
        }
    }
}
