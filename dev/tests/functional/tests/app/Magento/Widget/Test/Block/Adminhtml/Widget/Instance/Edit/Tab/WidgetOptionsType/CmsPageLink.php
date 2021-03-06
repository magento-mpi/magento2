<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType;


/**
 * Filling Widget Options that have cms page link type
 */
class CmsPageLink extends WidgetOptionsForm
{
    /**
     * Select block
     *
     * @var string
     */
    protected $selectBlock = '.scalable.btn-chooser';

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
    protected $pathToGrid = 'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\CmsPageLink\Grid';
    // @codingStandardsIgnoreEnd
}
