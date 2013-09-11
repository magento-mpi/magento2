<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Search Order Model
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Model\Search;

class Order extends \Magento\Object
{
    /**
     * Load search results
     *
     * @return \Magento\Adminhtml\Model\Search\Order
     */
    public function load()
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }

        $query = $this->getQuery();
        //TODO: add full name logic
        $collection = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Collection')
            ->addAttributeToSelect('*')
            ->addAttributeToSearchFilter(array(
                array('attribute' => 'increment_id',       'like'=>$query.'%'),
                array('attribute' => 'billing_firstname',  'like'=>$query.'%'),
                array('attribute' => 'billing_lastname',   'like'=>$query.'%'),
                array('attribute' => 'billing_telephone',  'like'=>$query.'%'),
                array('attribute' => 'billing_postcode',   'like'=>$query.'%'),

                array('attribute' => 'shipping_firstname', 'like'=>$query.'%'),
                array('attribute' => 'shipping_lastname',  'like'=>$query.'%'),
                array('attribute' => 'shipping_telephone', 'like'=>$query.'%'),
                array('attribute' => 'shipping_postcode',  'like'=>$query.'%'),
            ))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();

        foreach ($collection as $order) {
            $arr[] = array(
                'id'                => 'order/1/'.$order->getId(),
                'type'              => __('Order'),
                'name'              => __('Order #%1', $order->getIncrementId()),
                'description'       => $order->getBillingFirstname().' '.$order->getBillingLastname(),
                'form_panel_title'  => __('Order #%1 (%2)', $order->getIncrementId(), $order->getBillingFirstname().' '.$order->getBillingLastname()),
                'url' => \Mage::helper('Magento\Adminhtml\Helper\Data')->getUrl(
                    '*/sales_order/view',
                    array(
                        'order_id' => $order->getId()
                    )
                ),
            );
        }

        $this->setResults($arr);

        return $this;
    }
}
