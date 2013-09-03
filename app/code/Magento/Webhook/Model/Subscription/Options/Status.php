<?php
/**
 * Webhook subscription Options Status
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Subscription_Options_Status implements Magento_Core_Model_Option_ArrayInterface
{

    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @param Magento_Core_Model_Translate $translator
     */
    public function __construct(Magento_Core_Model_Translate $translator)
    {
        $this->_translator = $translator;
    }

    /**
     * Return statuses array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Magento_Webhook_Model_Subscription::STATUS_ACTIVE => __('Active'),
            Magento_Webhook_Model_Subscription::STATUS_REVOKED => __('Revoked'),
            Magento_Webhook_Model_Subscription::STATUS_INACTIVE => __('Inactive'),
        );
    }
}
