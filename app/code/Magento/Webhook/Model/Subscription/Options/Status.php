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
namespace Magento\Webhook\Model\Subscription\Options;

class Status implements \Magento\Core\Model\Option\ArrayInterface
{

    /**
     * @var \Magento\Core\Model\Translate
     */
    protected $_translator;

    /**
     * @param \Magento\Core\Model\Translate $translator
     */
    public function __construct(\Magento\Core\Model\Translate $translator)
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
            \Magento\Webhook\Model\Subscription::STATUS_ACTIVE => __('Active'),
            \Magento\Webhook\Model\Subscription::STATUS_REVOKED => __('Revoked'),
            \Magento\Webhook\Model\Subscription::STATUS_INACTIVE => __('Inactive'),
        );
    }
}
