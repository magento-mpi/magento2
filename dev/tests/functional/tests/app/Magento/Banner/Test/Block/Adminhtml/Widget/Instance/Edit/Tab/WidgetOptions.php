<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Block\Adminhtml\Widget\Instance\Edit\Tab;

use Mtf\Client\Element;
use Magento\Widget\Test\Fixture\Widget;
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptions as AbstractWidgetOptions;

/**
 * Widget options form for banner widget type
 */
class WidgetOptions extends AbstractWidgetOptions
{
    /**
     * Path for widget options tab
     *
     * @var string
     */
    protected $path = 'Magento\Banner\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\\';
}
