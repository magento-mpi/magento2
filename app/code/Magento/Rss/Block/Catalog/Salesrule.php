<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review form block
 */
class Magento_Rss_Block_Catalog_Salesrule extends Magento_Rss_Block_Abstract
{
    /**
     * @var Magento_Rss_Model_RssFactory
     */
    protected $_rssFactory;

    /**
     * @var Magento_SalesRule_Model_Resource_Rule_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Rss_Model_RssFactory $rssFactory
     * @param Magento_SalesRule_Model_Resource_Rule_CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Customer_Model_Session $customerSession,
        Magento_Rss_Model_RssFactory $rssFactory,
        Magento_SalesRule_Model_Resource_Rule_CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_rssFactory = $rssFactory;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($coreData, $context, $storeManager, $customerSession, $data);
    }

    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_salesrule_' . $this->getStoreId() . '_' . $this->_getCustomerGroupId());
        $this->setCacheLifetime(600);
    }

    /**
     * Generate RSS XML with sales rules data
     *
     * @return string
     */
    protected function _toHtml()
    {
        $storeId       = $this->_getStoreId();
        $storeModel    = $this->_storeManager->getStore($storeId);
        $websiteId     = $storeModel->getWebsiteId();
        $customerGroup = $this->_getCustomerGroupId();
        $now           = date('Y-m-d');
        $url           = $this->_urlBuilder->getUrl('');
        $newUrl        = $this->_urlBuilder->getUrl('rss/catalog/salesrule');
        $lang          = $storeModel->getConfig('general/locale/code');
        $title         = __('%1 - Discounts and Coupons', $storeModel->getName());

        /** @var $rssObject Magento_Rss_Model_Rss */
        $rssObject = $this->_rssFactory->create();
        $rssObject->_addHeader(array(
            'title'       => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
            'language'    => $lang
        ));

        /** @var $collection Magento_SalesRule_Model_Resource_Rule_Collection */
        $collection = $this->_collectionFactory->create();
        $collection->addWebsiteGroupDateFilter($websiteId, $customerGroup, $now)
            ->addFieldToFilter('is_rss', 1)
            ->setOrder('from_date','desc');
        $collection->load();

        /** @var $ruleModel Magento_SalesRule_Model_Rule */
        foreach ($collection as $ruleModel) {
            $description = '<table><tr>'
                . '<td style="text-decoration:none;">'.$ruleModel->getDescription()
                . '<br/>Discount Start Date: '.$this->formatDate($ruleModel->getFromDate(), 'medium');
            if ($ruleModel->getToDate()) {
                $description .= '<br/>Discount End Date: ' . $this->formatDate($ruleModel->getToDate(), 'medium');
            }
            if ($ruleModel->getCouponCode()) {
                $description .= '<br/> Coupon Code: '. $ruleModel->getCouponCode();
            }
            $description .=  '</td></tr></table>';
            $rssObject->_addEntry(array(
                'title'       => $ruleModel->getName(),
                'description' => $description,
                'link'        => $url
            ));
        }

        return $rssObject->createRssXml();
    }
}
