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
namespace Magento\Newsletter\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    const XML_PATH_TEMPLATE_FILTER = 'global/newsletter/tempate_filter';

    /**
     * Retrieve subsription confirmation url
     *
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @return string
     */
    public function getConfirmationUrl($subscriber)
    {
        return \Mage::getModel('Magento\Core\Model\Url')
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
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @return string
     */
    public function getUnsubscribeUrl($subscriber)
    {
        return \Mage::getModel('Magento\Core\Model\Url')
            ->setStore($subscriber->getStoreId())
            ->getUrl('newsletter/subscriber/unsubscribe', array(
                'id'     => $subscriber->getId(),
                'code'   => $subscriber->getCode(),
                '_nosid' => true
            ));
    }

    /**
     * Retrieve Template processor for Newsletter template
     *
     * @return \Magento\Filter\Template
     */
    public function getTemplateProcessor()
    {
        $model = (string)\Mage::getConfig()->getNode(self::XML_PATH_TEMPLATE_FILTER);
        return \Mage::getModel($model);
    }
}
