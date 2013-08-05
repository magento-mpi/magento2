<?php
/**
 * Newsletter status options
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Newsletter_Block_Subscribe_Grid_Options_Status implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * @var Mage_Newsletter_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Newsletter_Helper_Data $newsletterHelper
     */
    public function __construct(Mage_Newsletter_Helper_Data $newsletterHelper)
    {
        $this->_helper = $newsletterHelper;
    }

    /**
     * Return status column options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE   => __('Not Activated'),
            Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED   => __('Subscribed'),
            Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED => __('Unsubscribed'),
            Mage_Newsletter_Model_Subscriber::STATUS_UNCONFIRMED => __('Unconfirmed'),
        );
    }
}
