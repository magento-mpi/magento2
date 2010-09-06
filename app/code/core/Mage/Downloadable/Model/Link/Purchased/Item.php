<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable links purchased item model
 *
 * @method Mage_Downloadable_Model_Resource_Link_Purchased_Item _getResource()
 * @method Mage_Downloadable_Model_Resource_Link_Purchased_Item getResource()
 * @method Mage_Downloadable_Model_Link_Purchased_Item getPurchasedId()
 * @method int setPurchasedId(int $value)
 * @method Mage_Downloadable_Model_Link_Purchased_Item getOrderItemId()
 * @method int setOrderItemId(int $value)
 * @method Mage_Downloadable_Model_Link_Purchased_Item getProductId()
 * @method int setProductId(int $value)
 * @method Mage_Downloadable_Model_Link_Purchased_Item getLinkHash()
 * @method string setLinkHash(string $value)
 * @method Mage_Downloadable_Model_Link_Purchased_Item getNumberOfDownloadsBought()
 * @method int setNumberOfDownloadsBought(int $value)
 * @method Mage_Downloadable_Model_Link_Purchased_Item getNumberOfDownloadsUsed()
 * @method int setNumberOfDownloadsUsed(int $value)
 * @method Mage_Downloadable_Model_Link_Purchased_Item getLinkId()
 * @method int setLinkId(int $value)
 * @method Mage_Downloadable_Model_Link_Purchased_Item getLinkTitle()
 * @method string setLinkTitle(string $value)
 * @method Mage_Downloadable_Model_Link_Purchased_Item getIsShareable()
 * @method int setIsShareable(int $value)
 * @method Mage_Downloadable_Model_Link_Purchased_Item getLinkUrl()
 * @method string setLinkUrl(string $value)
 * @method Mage_Downloadable_Model_Link_Purchased_Item getLinkFile()
 * @method string setLinkFile(string $value)
 * @method Mage_Downloadable_Model_Link_Purchased_Item getLinkType()
 * @method string setLinkType(string $value)
 * @method Mage_Downloadable_Model_Link_Purchased_Item getStatus()
 * @method string setStatus(string $value)
 * @method Mage_Downloadable_Model_Link_Purchased_Item getCreatedAt()
 * @method string setCreatedAt(string $value)
 * @method Mage_Downloadable_Model_Link_Purchased_Item getUpdatedAt()
 * @method string setUpdatedAt(string $value)
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Link_Purchased_Item extends Mage_Core_Model_Abstract
{
    const XML_PATH_ORDER_ITEM_STATUS = 'catalog/downloadable/order_item_status';

    const LINK_STATUS_PENDING   = 'pending';
    const LINK_STATUS_AVAILABLE = 'available';
    const LINK_STATUS_EXPIRED   = 'expired';
    const LINK_STATUS_PENDING_PAYMENT = 'pending_payment';

    /**
     * Enter description here...
     *
     */
    protected function _construct()
    {
        $this->_init('downloadable/link_purchased_item');
        parent::_construct();
    }

}
