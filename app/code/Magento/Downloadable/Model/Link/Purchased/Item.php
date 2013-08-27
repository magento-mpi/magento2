<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable links purchased item model
 *
 * @method Magento_Downloadable_Model_Resource_Link_Purchased_Item _getResource()
 * @method Magento_Downloadable_Model_Resource_Link_Purchased_Item getResource()
 * @method int getPurchasedId()
 * @method Magento_Downloadable_Model_Link_Purchased_Item setPurchasedId(int $value)
 * @method int getOrderItemId()
 * @method Magento_Downloadable_Model_Link_Purchased_Item setOrderItemId(int $value)
 * @method int getProductId()
 * @method Magento_Downloadable_Model_Link_Purchased_Item setProductId(int $value)
 * @method string getLinkHash()
 * @method Magento_Downloadable_Model_Link_Purchased_Item setLinkHash(string $value)
 * @method int getNumberOfDownloadsBought()
 * @method Magento_Downloadable_Model_Link_Purchased_Item setNumberOfDownloadsBought(int $value)
 * @method int getNumberOfDownloadsUsed()
 * @method Magento_Downloadable_Model_Link_Purchased_Item setNumberOfDownloadsUsed(int $value)
 * @method int getLinkId()
 * @method Magento_Downloadable_Model_Link_Purchased_Item setLinkId(int $value)
 * @method string getLinkTitle()
 * @method Magento_Downloadable_Model_Link_Purchased_Item setLinkTitle(string $value)
 * @method int getIsShareable()
 * @method Magento_Downloadable_Model_Link_Purchased_Item setIsShareable(int $value)
 * @method string getLinkUrl()
 * @method Magento_Downloadable_Model_Link_Purchased_Item setLinkUrl(string $value)
 * @method string getLinkFile()
 * @method Magento_Downloadable_Model_Link_Purchased_Item setLinkFile(string $value)
 * @method string getLinkType()
 * @method Magento_Downloadable_Model_Link_Purchased_Item setLinkType(string $value)
 * @method string getStatus()
 * @method Magento_Downloadable_Model_Link_Purchased_Item setStatus(string $value)
 * @method string getCreatedAt()
 * @method Magento_Downloadable_Model_Link_Purchased_Item setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Magento_Downloadable_Model_Link_Purchased_Item setUpdatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Model_Link_Purchased_Item extends Magento_Core_Model_Abstract
{
    const XML_PATH_ORDER_ITEM_STATUS = 'catalog/downloadable/order_item_status';

    const LINK_STATUS_PENDING   = 'pending';
    const LINK_STATUS_AVAILABLE = 'available';
    const LINK_STATUS_EXPIRED   = 'expired';
    const LINK_STATUS_PENDING_PAYMENT = 'pending_payment';
    const LINK_STATUS_PAYMENT_REVIEW = 'payment_review';

    /**
     * Enter description here...
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Downloadable_Model_Resource_Link_Purchased_Item');
        parent::_construct();
    }

    /**
     * Check order item id
     *
     * @return Magento_Core_Model_Abstract
     */
    public function _beforeSave()
    {
        if (null == $this->getOrderItemId()) {
            throw new Exception(
                __('Order item id cannot be null'));
        }
        return parent::_beforeSave();
    }

}
