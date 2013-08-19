<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create order comment form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Create_Comment extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{
    protected $_form;

    public function getHeaderCssClass()
    {
        return 'head-comment';
    }

    public function getHeaderText()
    {
        return __('Order Comment');
    }

    public function getCommentNote()
    {
        return $this->escapeHtml($this->getQuote()->getCustomerNote());
    }

    public function getNoteNotify()
    {
        $notify = $this->getQuote()->getCustomerNoteNotify();
        if (is_null($notify) || $notify) {
            return true;
        }
        return false;
    }
}
