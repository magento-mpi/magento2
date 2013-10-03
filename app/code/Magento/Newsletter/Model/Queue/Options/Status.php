<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter Queue statuses option array
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Newsletter\Model\Queue\Options;

class Status implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Return statuses option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            \Magento\Newsletter\Model\Queue::STATUS_SENT 	=> __('Sent'),
            \Magento\Newsletter\Model\Queue::STATUS_CANCEL	=> __('Cancelled'),
            \Magento\Newsletter\Model\Queue::STATUS_NEVER 	=> __('Not Sent'),
            \Magento\Newsletter\Model\Queue::STATUS_SENDING => __('Sending'),
            \Magento\Newsletter\Model\Queue::STATUS_PAUSE 	=> __('Paused'),
        );
    }
}
