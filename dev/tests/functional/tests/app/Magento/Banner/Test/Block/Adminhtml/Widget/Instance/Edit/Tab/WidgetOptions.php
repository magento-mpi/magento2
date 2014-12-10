<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Block\Adminhtml\Widget\Instance\Edit\Tab;

use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptions as AbstractWidgetOptions;
use Magento\Widget\Test\Fixture\Widget;

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
