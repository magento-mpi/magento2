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
 *
 * @category   Magento
 * @package    Magento_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rss_Block_Catalog_Salesrule extends Magento_Rss_Block_Abstract
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_salesrule_'.$this->getStoreId().'_'.$this->_getCustomerGroupId());
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
        $websiteId     = Mage::app()->getStore($storeId)->getWebsiteId();
        $customerGroup = $this->_getCustomerGroupId();
        $now           = date('Y-m-d');
        $url           = Mage::getUrl('');
        $newUrl        = Mage::getUrl('rss/catalog/salesrule');
        $lang          = $this->_coreStoreConfig->getConfig('general/locale/code');
        $title       = __('%1 - Discounts and Coupons',Mage::app()->getStore($storeId)->getName());

        /** @var $rssObject Magento_Rss_Model_Rss */
        $rssObject = Mage::getModel('Magento_Rss_Model_Rss');
        /** @var $collection Magento_SalesRule_Model_Resource_Rule_Collection */
        $collection = Mage::getModel('Magento_SalesRule_Model_Rule')->getResourceCollection();

        $data = array(
            'title'       => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
            'language'    => $lang
        );
        $rssObject->_addHeader($data);

        $collection->addWebsiteGroupDateFilter($websiteId, $customerGroup, $now)
            ->addFieldToFilter('is_rss', 1)
            ->setOrder('from_date','desc');
        $collection->load();

        foreach ($collection as $sr) {
            $description = '<table><tr>'.
            '<td style="text-decoration:none;">'.$sr->getDescription().
            '<br/>Discount Start Date: '.$this->formatDate($sr->getFromDate(), 'medium').
            ( $sr->getToDate() ? ('<br/>Discount End Date: '.$this->formatDate($sr->getToDate(), 'medium')):'').
            ($sr->getCouponCode() ? '<br/> Coupon Code: '.$sr->getCouponCode().'' : '').
            '</td>'.
            '</tr></table>';
             $data = array(
                 'title'       => $sr->getName(),
                 'description' => $description,
                 'link'        => $url
             );
            $rssObject->_addEntry($data);
        }

        return $rssObject->createRssXml();
    }
}
