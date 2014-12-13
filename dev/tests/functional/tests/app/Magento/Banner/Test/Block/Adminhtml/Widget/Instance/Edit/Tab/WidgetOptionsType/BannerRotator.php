<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType;

use Magento\Banner\Test\Block\Adminhtml\Banner\Grid;
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\WidgetOptionsForm;
use Mtf\Client\Element;

/**
 * Filling Widget Options that have banner rotator type
 */
class BannerRotator extends WidgetOptionsForm
{
    /**
     * Banner Rotator grid block
     *
     * @var string
     */
    protected $gridBlock = '#bannerGrid';

    /**
     * Path to grid
     *
     * @var string
     */
    protected $pathToGrid = 'Magento\Banner\Test\Block\Adminhtml\Banner\Grid';

    /**
     * Select node on widget options tab
     *
     * @param array $entities
     * @return void
     */
    protected function selectEntity(array $entities)
    {
        foreach ($entities['value'] as $entity) {
            /** @var Grid $bannerRotatorGrid */
            $bannerRotatorGrid = $this->blockFactory->create(
                $this->pathToGrid,
                ['element' => $this->_rootElement->find($this->gridBlock)]
            );
            $bannerRotatorGrid->searchAndSelect(['banner' => $entity->getName()]);
        }
    }
}
