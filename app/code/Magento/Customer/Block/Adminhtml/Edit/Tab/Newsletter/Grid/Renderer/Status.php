<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter queue grid block status item renderer
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter\Grid\Renderer;

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

    public static function  getStatus($status)
    {
        if(isset(self::$_statuses[$status])) {
            return self::$_statuses[$status];
        }

        return __('Unknown');
    }

}
