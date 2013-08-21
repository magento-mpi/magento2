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

class Magento_Newsletter_Model_Queue_Options_Status implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Return statuses option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Magento_Newsletter_Model_Queue::STATUS_SENT 	=> __('Sent'),
            Magento_Newsletter_Model_Queue::STATUS_CANCEL	=> __('Cancelled'),
            Magento_Newsletter_Model_Queue::STATUS_NEVER 	=> __('Not Sent'),
            Magento_Newsletter_Model_Queue::STATUS_SENDING => __('Sending'),
            Magento_Newsletter_Model_Queue::STATUS_PAUSE 	=> __('Paused'),
        );
    }
}
