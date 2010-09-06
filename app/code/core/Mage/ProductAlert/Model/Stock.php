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
 * @package     Mage_ProductAlert
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * ProductAlert for back in stock model
 *
 * @method Mage_ProductAlert_Model_Resource_Stock _getResource()
 * @method Mage_ProductAlert_Model_Resource_Stock getResource()
 * @method Mage_ProductAlert_Model_Stock getCustomerId()
 * @method int setCustomerId(int $value)
 * @method Mage_ProductAlert_Model_Stock getProductId()
 * @method int setProductId(int $value)
 * @method Mage_ProductAlert_Model_Stock getWebsiteId()
 * @method int setWebsiteId(int $value)
 * @method Mage_ProductAlert_Model_Stock getAddDate()
 * @method string setAddDate(string $value)
 * @method Mage_ProductAlert_Model_Stock getSendDate()
 * @method string setSendDate(string $value)
 * @method Mage_ProductAlert_Model_Stock getSendCount()
 * @method int setSendCount(int $value)
 * @method Mage_ProductAlert_Model_Stock getStatus()
 * @method int setStatus(int $value)
 *
 * @category    Mage
 * @package     Mage_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ProductAlert_Model_Stock extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('productalert/stock');
    }

    public function getCustomerCollection()
    {
        return Mage::getResourceModel('productalert/stock_customer_collection');
    }

    public function loadByParam()
    {
        if (!is_null($this->getProductId()) && !is_null($this->getCustomerId()) && !is_null($this->getWebsiteId())) {
            $this->getResource()->loadByParam($this);
        }
        return $this;
    }

    public function deleteCustomer($customerId, $websiteId = 0)
    {
        $this->getResource()->deleteCustomer($this, $customerId, $websiteId);
        return $this;
    }
}
