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
    const XML_PATH_TEMPLATE_FILTER = 'global/newsletter/tempate_filter';

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config $coreConfig
    ) {
        parent::__construct(
            $context
        );
        $this->_coreConfig = $coreConfig;
    }

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

    /**
     * Retrieve Template processor for Newsletter template
     *
     * @return Magento_Filter_Template
     */
    public function getTemplateProcessor()
    {
        $model = (string)$this->_coreConfig->getNode(self::XML_PATH_TEMPLATE_FILTER);
        return Mage::getModel($model);
    }
}
