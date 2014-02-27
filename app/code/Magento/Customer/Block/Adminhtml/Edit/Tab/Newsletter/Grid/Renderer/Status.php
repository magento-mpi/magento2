<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter\Grid\Renderer;

/**
 * Adminhtml newsletter queue grid block status item renderer
 */
class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected static $_statuses;

    protected function _construct()
    {
        self::$_statuses = array(
                \Magento\Newsletter\Model\Queue::STATUS_SENT 	=> __('Sent'),
                \Magento\Newsletter\Model\Queue::STATUS_CANCEL	=> __('Cancel'),
                \Magento\Newsletter\Model\Queue::STATUS_NEVER 	=> __('Not Sent'),
                \Magento\Newsletter\Model\Queue::STATUS_SENDING => __('Sending'),
                \Magento\Newsletter\Model\Queue::STATUS_PAUSE 	=> __('Paused'),
            );
        parent::_construct();
    }

    public function render(\Magento\Object $row)
    {
        return __($this->getStatus($row->getQueueStatus()));
    }

    public static function getStatus($status)
    {
        if (isset(self::$_statuses[$status])) {
            return self::$_statuses[$status];
        }

        return __('Unknown');
    }
}
