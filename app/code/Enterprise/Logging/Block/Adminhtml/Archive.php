<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Actions Log grid container
 *
 */
class Enterprise_Logging_Block_Adminhtml_Archive extends Magento_Adminhtml_Block_Widget_Container
{
    /**
     * Header text getter
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Admin Actions Log Archive');
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
