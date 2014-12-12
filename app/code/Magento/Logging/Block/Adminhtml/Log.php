<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Log grid container
 */
namespace Magento\Logging\Block\Adminhtml;

class Log extends \Magento\Backend\Block\Widget\Container
{
    /**
     * Header text getter
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Admin Actions Log');
    }

    /**
     * Grid contents getter
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml();
    }
}
