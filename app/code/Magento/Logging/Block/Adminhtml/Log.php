<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Log grid container
 */
class Magento_Logging_Block_Adminhtml_Log extends Magento_Adminhtml_Block_Widget_Container
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
