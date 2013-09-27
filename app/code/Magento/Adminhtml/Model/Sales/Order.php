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
 * Order control model
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Model_Sales_Order
{
    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_session;

    /**
     * @var Magento_Customer_Model_CustomerFactory]
     */
    protected $_customerFactory;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_Backend_Model_Session $session
     */
    public function __construct(
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Backend_Model_Session $session
    ) {
        $this->_productFactory = $productFactory;
        $this->_customerFactory = $customerFactory;
        $this->_session = $session;
    }

    public function checkRelation(Magento_Sales_Model_Order $order)
    {
        /**
         * Check customer existing
         */
        $customer = $this->_customerFactory->create()->load($order->getCustomerId());
        if (!$customer->getId()) {
            $this->_session->addNotice(
                __(' The customer does not exist in the system anymore.')
            );
        }

        /**
         * Check Item products existing
         */
        $productIds = array();
        foreach ($order->getAllItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        $productCollection = $this->_productFactory->create()->getCollection()
            ->addIdFilter($productIds)
            ->load();

        $hasBadItems = false;
        foreach ($order->getAllItems() as $item) {
            if (!$productCollection->getItemById($item->getProductId())) {
                $this->_session->addError(
                   __('The item %1 (SKU %2) does not exist in the catalog anymore.', $item->getName(), $item->getSku()
                ));
                $hasBadItems = true;
            }
        }
        if ($hasBadItems) {
            $this->_session->addError(
                __('Some items in this order are no longer in our catalog.')
            );
        }
        return $this;
    }

}
