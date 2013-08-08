<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * ProductAlert for back in stock model
 *
 * @method Mage_ProductAlert_Model_Resource_Stock _getResource()
 * @method Mage_ProductAlert_Model_Resource_Stock getResource()
 * @method int getCustomerId()
 * @method Mage_ProductAlert_Model_Stock setCustomerId(int $value)
 * @method int getProductId()
 * @method Mage_ProductAlert_Model_Stock setProductId(int $value)
 * @method int getWebsiteId()
 * @method Mage_ProductAlert_Model_Stock setWebsiteId(int $value)
 * @method string getAddDate()
 * @method Mage_ProductAlert_Model_Stock setAddDate(string $value)
 * @method string getSendDate()
 * @method Mage_ProductAlert_Model_Stock setSendDate(string $value)
 * @method int getSendCount()
 * @method Mage_ProductAlert_Model_Stock setSendCount(int $value)
 * @method int getStatus()
 * @method Mage_ProductAlert_Model_Stock setStatus(int $value)
 *
 * @category    Mage
 * @package     Mage_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ProductAlert_Model_Stock extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_ProductAlert_Model_Resource_Stock');
    }

    public function getCustomerCollection()
    {
        return Mage::getResourceModel('Mage_ProductAlert_Model_Resource_Stock_Customer_Collection');
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
