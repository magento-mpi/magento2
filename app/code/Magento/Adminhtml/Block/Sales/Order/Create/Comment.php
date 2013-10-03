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
namespace Magento\Adminhtml\Block\Sales\Order\Create;

class Comment extends \Magento\Adminhtml\Block\Sales\Order\Create\AbstractCreate
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
