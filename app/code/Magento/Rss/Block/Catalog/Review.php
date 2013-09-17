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
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rss_Block_Catalog_Review extends Magento_Core_Block_Abstract
{
    /**
     * Rss data
     *
     * @var Magento_Rss_Helper_Data
     */
    protected $_rssData = null;

    /**
     * Adminhtml data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_adminhtmlData = null;

    /**
     * @param Magento_Backend_Helper_Data $adminhtmlData
     * @param Magento_Rss_Helper_Data $rssData
     * @param Magento_Core_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Helper_Data $adminhtmlData,
        Magento_Rss_Helper_Data $rssData,
        Magento_Core_Block_Context $context,
        array $data = array()
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        $this->_rssData = $rssData;
        parent::__construct($context, $data);
    }

    /**
     * Render XML response
     *
     * @return string
     */
    protected function _toHtml()
    {
        $newUrl = Mage::getUrl('rss/catalog/review');
        $title = __('Pending product review(s)');
        $this->_rssData->disableFlat();

        $rssObj = Mage::getModel('Magento_Rss_Model_Rss');
        $data = array(
            'title' => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
        );
        $rssObj->_addHeader($data);

        $reviewModel = Mage::getModel('Magento_Review_Model_Review');

        $collection = $reviewModel->getProductCollection()
            ->addStatusFilter($reviewModel->getPendingStatus())
            ->addAttributeToSelect('name', 'inner')
            ->setDateOrder();

        $this->_eventManager->dispatch('rss_catalog_review_collection_select', array('collection' => $collection));

        Mage::getSingleton('Magento_Core_Model_Resource_Iterator')->walk(
            $collection->getSelect(),
            array(array($this, 'addReviewItemXmlCallback')),
            array('rssObj'=> $rssObj, 'reviewModel'=> $reviewModel));
        return $rssObj->createRssXml();
    }

    /**
     * Format single RSS element
     *
     * @param array $args
     * @return null
     */
    public function addReviewItemXmlCallback($args)
    {
        $rssObj = $args['rssObj'];
        $row = $args['row'];

        $store = Mage::app()->getStore($row['store_id']);
        $urlModel = Mage::getModel('Magento_Core_Model_Url')->setStore($store);
        $productUrl = $urlModel->getUrl('catalog/product/view', array('id' => $row['entity_id']));
        $reviewUrl = $this->_adminhtmlData->getUrl(
            'adminhtml/catalog_product_review/edit/',
            array('id' => $row['review_id'], '_secure' => true, '_nosecret' => true));
        $storeName = $store->getName();

        $description = '<p>'
                     . __('Product: <a href="%1">%2</a> <br/>', $productUrl, $row['name'])
                     . __('Summary of review: %1 <br/>', $row['title'])
                     . __('Review: %1 <br/>', $row['detail'])
                     . __('Store: %1 <br/>', $storeName )
                     . __('Click <a href="%1">here</a> to view the review.', $reviewUrl)
                     . '</p>';
        $data = array(
            'title'         => __('Product: "%1" review By: %2', $row['name'], $row['nickname']),
            'link'          => 'test',
            'description'   => $description,
        );
        $rssObj->_addEntry($data);
    }
}
