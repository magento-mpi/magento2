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
 * @method \Magento\Downloadable\Model\Resource\Link\Purchased\Item _getResource()
 * @method \Magento\Downloadable\Model\Resource\Link\Purchased\Item getResource()
 * @method int getPurchasedId()
 * @method \Magento\Downloadable\Model\Link\Purchased\Item setPurchasedId(int $value)
 * @method int getOrderItemId()
 * @method \Magento\Downloadable\Model\Link\Purchased\Item setOrderItemId(int $value)
 * @method int getProductId()
 * @method \Magento\Downloadable\Model\Link\Purchased\Item setProductId(int $value)
 * @method string getLinkHash()
 * @method \Magento\Downloadable\Model\Link\Purchased\Item setLinkHash(string $value)
 * @method int getNumberOfDownloadsBought()
 * @method \Magento\Downloadable\Model\Link\Purchased\Item setNumberOfDownloadsBought(int $value)
 * @method int getNumberOfDownloadsUsed()
 * @method \Magento\Downloadable\Model\Link\Purchased\Item setNumberOfDownloadsUsed(int $value)
 * @method int getLinkId()
 * @method \Magento\Downloadable\Model\Link\Purchased\Item setLinkId(int $value)
 * @method string getLinkTitle()
 * @method \Magento\Downloadable\Model\Link\Purchased\Item setLinkTitle(string $value)
 * @method int getIsShareable()
 * @method \Magento\Downloadable\Model\Link\Purchased\Item setIsShareable(int $value)
 * @method string getLinkUrl()
 * @method \Magento\Downloadable\Model\Link\Purchased\Item setLinkUrl(string $value)
 * @method string getLinkFile()
 * @method \Magento\Downloadable\Model\Link\Purchased\Item setLinkFile(string $value)
 * @method string getLinkType()
 * @method \Magento\Downloadable\Model\Link\Purchased\Item setLinkType(string $value)
 * @method string getStatus()
 * @method \Magento\Downloadable\Model\Link\Purchased\Item setStatus(string $value)
 * @method string getCreatedAt()
 * @method \Magento\Downloadable\Model\Link\Purchased\Item setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method \Magento\Downloadable\Model\Link\Purchased\Item setUpdatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Model\Link\Purchased;

class Item extends \Magento\Core\Model\AbstractModel
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
        $this->_init('\Magento\Downloadable\Model\Resource\Link\Purchased\Item');
        parent::_construct();
    }

    /**
     * Check order item id
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    public function _beforeSave()
    {
        if (null == $this->getOrderItemId()) {
            throw new \Exception(
                __('Order item id cannot be null'));
        }
        return parent::_beforeSave();
    }

}
