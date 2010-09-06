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
 * Downloadable links purchased model
 *
 * @method Mage_Downloadable_Model_Resource_Link_Purchased _getResource()
 * @method Mage_Downloadable_Model_Resource_Link_Purchased getResource()
 * @method Mage_Downloadable_Model_Link_Purchased getOrderId()
 * @method int setOrderId(int $value)
 * @method Mage_Downloadable_Model_Link_Purchased getOrderIncrementId()
 * @method string setOrderIncrementId(string $value)
 * @method Mage_Downloadable_Model_Link_Purchased getOrderItemId()
 * @method int setOrderItemId(int $value)
 * @method Mage_Downloadable_Model_Link_Purchased getCreatedAt()
 * @method string setCreatedAt(string $value)
 * @method Mage_Downloadable_Model_Link_Purchased getUpdatedAt()
 * @method string setUpdatedAt(string $value)
 * @method Mage_Downloadable_Model_Link_Purchased getCustomerId()
 * @method int setCustomerId(int $value)
 * @method Mage_Downloadable_Model_Link_Purchased getProductName()
 * @method string setProductName(string $value)
 * @method Mage_Downloadable_Model_Link_Purchased getProductSku()
 * @method string setProductSku(string $value)
 * @method Mage_Downloadable_Model_Link_Purchased getLinkSectionTitle()
 * @method string setLinkSectionTitle(string $value)
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Link_Purchased extends Mage_Core_Model_Abstract
{
    /**
     * Enter description here...
     *
     */
    protected function _construct()
    {
        $this->_init('downloadable/link_purchased');
        parent::_construct();
    }

}
