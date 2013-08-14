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
 * @category   Mage
 * @package    Magento_Newsletter
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Newsletter_Model_Queue_Options_Status implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Newsletter Helper Data
     *
     * @var Magento_Newsletter_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_Newsletter_Helper_Data $newsletterHelper
     */
    public function __construct(Magento_Newsletter_Helper_Data $newsletterHelper)
    {
        $this->_helper = $newsletterHelper;
    }

    /**
     * Return statuses option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Magento_Newsletter_Model_Queue::STATUS_SENT 	=> $this->_helper->__('Sent'),
            Magento_Newsletter_Model_Queue::STATUS_CANCEL	=> $this->_helper->__('Cancelled'),
            Magento_Newsletter_Model_Queue::STATUS_NEVER 	=> $this->_helper->__('Not Sent'),
            Magento_Newsletter_Model_Queue::STATUS_SENDING => $this->_helper->__('Sending'),
            Magento_Newsletter_Model_Queue::STATUS_PAUSE 	=> $this->_helper->__('Paused'),
        );
    }
}
