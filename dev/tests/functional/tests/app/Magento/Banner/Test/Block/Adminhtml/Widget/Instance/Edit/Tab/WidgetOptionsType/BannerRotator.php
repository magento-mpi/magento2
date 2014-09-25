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
                /** @var Grid $bannerRotatorGrid */
                $bannerRotatorGrid = $this->blockFactory->create(
                    'Magento\Banner\Test\Block\Adminhtml\Banner\Grid',
                    [
                        'element' => $this->_rootElement
                            ->find($this->bannerRotatorGrid)
                    ]
                );
                $bannerRotatorGrid->searchAndSelect(['banner' => $field['value']['name']]);
            } else {
                parent::_fill([$name => $field], $context);
            }
        }
    }
}
