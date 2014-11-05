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
use Mtf\Fixture\InjectableFixture;

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
