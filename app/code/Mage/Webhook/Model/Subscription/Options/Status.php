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
     * @var Mage_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @param Mage_Core_Model_Translate $translator
     */
    public function __construct(Mage_Core_Model_Translate $translator)
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
            Mage_Webhook_Model_Subscription::STATUS_ACTIVE => __('Active'),
            Mage_Webhook_Model_Subscription::STATUS_REVOKED => __('Revoked'),
            Mage_Webhook_Model_Subscription::STATUS_INACTIVE => __('Inactive'),
        );
    }
}
