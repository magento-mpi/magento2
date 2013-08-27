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
class Magento_Rss_Block_Order_New extends Magento_Core_Block_Abstract
{
    /**
     * Adminhtml data
     *
     * @var Magento_Adminhtml_Helper_Data
     */
    protected $_adminhtmlData = null;

    /**
     * @param Magento_Adminhtml_Helper_Data $adminhtmlData
     * @param Magento_Core_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Helper_Data $adminhtmlData,
        Magento_Core_Block_Context $context,
        array $data = array()
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        $order = Mage::getModel('Magento_Sales_Model_Order');
        $passDate = $order->getResource()->formatDate(mktime(0,0,0,date('m'),date('d')-7));

        $newurl = $this->_adminhtmlData->getUrl(
            'adminhtml/sales_order',
            array(
                '_secure' => true,
                '_nosecret' => true
            )
        );
        $title = __('New Orders');

        $rssObj = Mage::getModel('Magento_Rss_Model_Rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                );
        $rssObj->_addHeader($data);

        $collection = $order->getCollection()
            ->addAttributeToFilter('created_at', array('date'=>true, 'from'=> $passDate))
            ->addAttributeToSort('created_at','desc')
        ;

        $detailBlock = Mage::getBlockSingleton('Magento_Rss_Block_Order_Details');

        Mage::dispatchEvent('rss_order_new_collection_select', array('collection' => $collection));

        Mage::getSingleton('Magento_Core_Model_Resource_Iterator')
            ->walk($collection->getSelect(), array(array($this, 'addNewOrderXmlCallback')), array('rssObj'=> $rssObj, 'order'=>$order , 'detailBlock' => $detailBlock));

        return $rssObj->createRssXml();
    }

    public function addNewOrderXmlCallback($args)
    {
        $rssObj = $args['rssObj'];
        $order = $args['order'];
        $detailBlock = $args['detailBlock'];
        $order->reset()->load($args['row']['entity_id']);
        if ($order && $order->getId()) {
            $title = __('Order #%1 created at %2', $order->getIncrementId(), $this->formatDate($order->getCreatedAt()));
            $url = $this->_adminhtmlData->getUrl(
                'adminhtml/sales_order/view',
                array(
                    '_secure' => true,
                    'order_id' => $order->getId(),
                    '_nosecret' => true
                )
            );
            $detailBlock->setOrder($order);
            $data = array(
                    'title'         => $title,
                    'link'          => $url,
                    'description'   => $detailBlock->toHtml()
                    );
            $rssObj->_addEntry($data);
        }
    }
}
