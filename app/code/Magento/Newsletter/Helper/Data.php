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
     * Url
     *
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_url;

    /**
     * Template filter factory
     *
     * @var Magento_Newsletter_Model_Template_Filter_Factory
     */
    protected $_templateFilterFactory;

    /**
     * Constructor
     *
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Core_Model_UrlInterface $url
     * @param Magento_Newsletter_Model_Template_Filter_Factory $templateFilterFactory
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config $coreConfig,
        Magento_Core_Model_UrlInterface $url,
        Magento_Newsletter_Model_Template_Filter_Factory $templateFilterFactory
    ) {
        parent::__construct($context);
        $this->_coreConfig = $coreConfig;
        $this->_url = $url;
        $this->_templateFilterFactory = $templateFilterFactory;
    }

    /**
     * Retrieve subsription confirmation url
     *
     * @param Magento_Newsletter_Model_Subscriber $subscriber
     * @return string
     */
    public function getConfirmationUrl($subscriber)
    {
        return $this->_url->setStore($subscriber->getStoreId())
            ->getUrl('newsletter/subscriber/confirm', array(
                'id'     => $subscriber->getId(),
                'code'   => $subscriber->getCode(),
                '_nosid' => true,
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
        return $this->_url->setStore($subscriber->getStoreId())
            ->getUrl('newsletter/subscriber/unsubscribe', array(
                'id'     => $subscriber->getId(),
                'code'   => $subscriber->getCode(),
                '_nosid' => true,
            ));
    }

    /**
     * Retrieve Template processor for Newsletter template
     *
     * @return Magento_Filter_Template
     */
    public function getTemplateProcessor()
    {
        $name = (string)$this->_coreConfig->getNode(self::XML_PATH_TEMPLATE_FILTER);
        return $this->_templateFilterFactory->create($name);
    }
}
