<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType;

use Mtf\Client\Element;
use Mtf\Fixture\InjectableFixture;

/**
 * Filling Widget Options that have cms static block type
 */
class CmsStaticBlock extends WidgetOptionsForm
{
    /**
     * Select block
     *
     * @var string
     */
    protected $selectBlock = '.action-.scalable.btn-chooser';

    /**
     * Cms Page Link grid block
     *
     * @var string
     */
    protected $gridBlock = './ancestor::body//*[contains(@id, "responseCntoptions_fieldset")]';

    /**
     * Path to grid
     *
     * @var string
     */
    // @codingStandardsIgnoreStart
    protected $pathToGrid = 'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CmsStaticBlock\Grid';
    // @codingStandardsIgnoreEnd

    /**
     * Prepare filter for grid
     *
     * @param InjectableFixture $entity
     * @return array
     */
    protected function prepareFilter(InjectableFixture $entity)
    {
        return ['title' => $entity->getTitle(), 'identifier' => $entity->getIdentifier()];
    }
}
