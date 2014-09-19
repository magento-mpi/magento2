<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Block\Catalog;

/**
 * Review form block
 */
class Salesrule extends \Magento\Rss\Block\AbstractBlock
{
    /**
     * @var \Magento\Rss\Model\RssFactory
     */
    protected $_rssFactory;

    /**
     * @var \Magento\SalesRule\Model\Resource\Rule\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rss\Model\RssFactory $rssFactory
     * @param \Magento\SalesRule\Model\Resource\Rule\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rss\Model\RssFactory $rssFactory,
        \Magento\SalesRule\Model\Resource\Rule\CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_rssFactory = $rssFactory;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $httpContext, $data);
    }

    /**
     * @return void
     */
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
        $storeId = $this->_getStoreId();
        $storeModel = $this->_storeManager->getStore($storeId);
        $websiteId = $storeModel->getWebsiteId();
        $customerGroup = $this->_getCustomerGroupId();
        $now = date('Y-m-d');
        $url = $this->_urlBuilder->getUrl('');
        $newUrl = $this->_urlBuilder->getUrl('rss/catalog/salesrule');
        $title = __('%1 - Discounts and Coupons', $storeModel->getName());
        $lang = $this->_scopeConfig->getValue(
            \Magento\Core\Helper\Data::XML_PATH_DEFAULT_LOCALE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeModel
        );

        /** @var $rssObject \Magento\Rss\Model\Rss */
        $rssObject = $this->_rssFactory->create();
        $rssObject->_addHeader(
            array(
                'title' => $title,
                'description' => $title,
                'link' => $newUrl,
                'charset' => 'UTF-8',
                'language' => $lang
            )
        );

        /** @var $collection \Magento\SalesRule\Model\Resource\Rule\Collection */
        $collection = $this->_collectionFactory->create();
        $collection->addWebsiteGroupDateFilter(
            $websiteId,
            $customerGroup,
            $now
        )->addFieldToFilter(
            'is_rss',
            1
        )->setOrder(
            'from_date',
            'desc'
        );
        $collection->load();

        /** @var $ruleModel \Magento\SalesRule\Model\Rule */
        foreach ($collection as $ruleModel) {
            $description = '<table><tr>' . '<td style="text-decoration:none;">' . $ruleModel->getDescription()
                . '<br/>Discount Start Date: ' . $this->formatDate(
                    $ruleModel->getFromDate(),
                    'medium'
                );
            if ($ruleModel->getToDate()) {
                $description .= '<br/>Discount End Date: ' . $this->formatDate($ruleModel->getToDate(), 'medium');
            }
            if ($ruleModel->getCouponCode()) {
                $description .= '<br/> Coupon Code: ' . $ruleModel->getCouponCode();
            }
            $description .= '</td></tr></table>';
            $rssObject->_addEntry(
                array('title' => $ruleModel->getName(), 'description' => $description, 'link' => $url)
            );
        }

        return $rssObject->createRssXml();
    }
}
