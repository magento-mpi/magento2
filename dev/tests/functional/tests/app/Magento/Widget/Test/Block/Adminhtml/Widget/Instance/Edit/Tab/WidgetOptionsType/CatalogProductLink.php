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
use Mtf\Fixture\InjectableFixture;

/**
 * Filling Widget Options that have catalog product link type
 */
class CatalogProductLink extends WidgetOptionsForm
{
    /**
     * Select block
     *
     * @var string
     */
    protected $selectBlock = '.action-.scalable.btn-chooser';

    /**
     * Catalog Product Link grid block
     *
     * @var string
     */
    protected $gridBlock = './ancestor::body//*[contains(@id, "options_fieldset")]//div[contains(@class, "main-col")]';

    /**
     * Path to grid
     *
     * @var string
     */
    // @codingStandardsIgnoreStart
    protected $pathToGrid = 'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CatalogProductLink\Grid';
    // @codingStandardsIgnoreEnd

    /**
     * Prepare filter for grid
     *
     * @param InjectableFixture $entity
     * @return array
     */
    protected function prepareFilter(InjectableFixture $entity)
    {
        return ['name' => $entity->getName()];
    }
}
