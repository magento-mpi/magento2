<?php
/**
 * Webhook subscription Options Status
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Subscription_Options_Status implements Mage_Core_Model_Option_ArrayInterface
{

    /**
     * @var Mage_Webhook_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Webhook_Helper_Data $helper
     */
    public function __construct(Mage_Webhook_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Return statuses array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Mage_Webhook_Model_Subscription::STATUS_ACTIVE => $this->_helper->__('Active'),
            Mage_Webhook_Model_Subscription::STATUS_REVOKED => $this->_helper->__('Revoked'),
            Mage_Webhook_Model_Subscription::STATUS_INACTIVE => $this->_helper->__('Inactive'),
        );
    }
}
