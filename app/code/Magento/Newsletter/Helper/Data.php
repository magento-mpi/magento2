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
 * Newsletter Data Helper
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Newsletter_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Retrieve subsription confirmation url
     *
     * @param Magento_Newsletter_Model_Subscriber $subscriber
     * @return string
     */
    public function getConfirmationUrl($subscriber)
    {
        return Mage::getModel('Magento_Core_Model_Url')
            ->setStore($subscriber->getStoreId())
            ->getUrl('newsletter/subscriber/confirm', array(
                'id'     => $subscriber->getId(),
                'code'   => $subscriber->getCode(),
                '_nosid' => true
            ));
    }

    /**
     * Retrieve unsubsription url
     *
     * @param Magento_Newsletter_Model_Subscriber $subscriber
     * @return string
     */
    public function getUnsubscribeUrl($subscriber)
    {
        return Mage::getModel('Magento_Core_Model_Url')
            ->setStore($subscriber->getStoreId())
            ->getUrl('newsletter/subscriber/unsubscribe', array(
                'id'     => $subscriber->getId(),
                'code'   => $subscriber->getCode(),
                '_nosid' => true
            ));
    }
}
