<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Block_Catalog_Salesrule extends Mage_Rss_Block_Abstract
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_salesrule_'.$this->getStoreId().'_'.$this->_getCustomerGroupId());
        $this->setCacheLifetime(600);
    }


    protected function _toHtml()
    {
        //store id is store view id
        $storeId = $this->_getStoreId();
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        //customer group id
        $custGroup =   $this->_getCustomerGroupId();

        $newurl = Mage::getUrl('rss/catalog/salesrule');
        $title = Mage::helper('Mage_Rss_Helper_Data')->__('%s - Discounts and Coupons',Mage::app()->getStore($storeId)->getName());
        $lang = Mage::getStoreConfig('general/locale/code');

        $rssObj = Mage::getModel('Mage_Rss_Model_Rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                'language'    => $lang
                );
        $rssObj->_addHeader($data);

        $now = date('Y-m-d');
        $_saleRule = Mage::getModel('Mage_SalesRule_Model_Rule');

        $collection = $_saleRule->getResourceCollection()
                    ->addFieldToFilter('from_date', array('date'=>true, 'to' => $now))
                    ->addFieldToFilter('website_ids',array('finset' => $websiteId))
                    ->addFieldToFilter('customer_group_ids', array('finset' => $custGroup))
                    ->addFieldToFilter('is_rss',1)
                    ->addFieldToFilter('is_active',1)
                    ->setOrder('from_date','desc');

        $collection->getSelect()->where('to_date is null or to_date>=?', $now);
        $collection->load();

        $url = Mage::getUrl('');

        foreach ($collection as $sr) {
            $description = '<table><tr>'.
            '<td style="text-decoration:none;">'.$sr->getDescription().
            '<br/>Discount Start Date: '.$this->formatDate($sr->getFromDate(), 'medium').
            ( $sr->getToDate() ? ('<br/>Discount End Date: '.$this->formatDate($sr->getToDate(), 'medium')):'').
            ($sr->getCouponCode() ? '<br/> Coupon Code: '.$sr->getCouponCode().'' : '').
            '</td>'.
            '</tr></table>';
             $data = array(
                'title'         => $sr->getName(),
                'description'   => $description,
                'link'          => $url
                );
            $rssObj->_addEntry($data);
        }
        return $rssObj->createRssXml();
    }
}
