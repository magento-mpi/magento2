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
 * Downloadable links purchased model
 *
 * @method Magento_Downloadable_Model_Resource_Link_Purchased _getResource()
 * @method Magento_Downloadable_Model_Resource_Link_Purchased getResource()
 * @method int getOrderId()
 * @method Magento_Downloadable_Model_Link_Purchased setOrderId(int $value)
 * @method string getOrderIncrementId()
 * @method Magento_Downloadable_Model_Link_Purchased setOrderIncrementId(string $value)
 * @method int getOrderItemId()
 * @method Magento_Downloadable_Model_Link_Purchased setOrderItemId(int $value)
 * @method string getCreatedAt()
 * @method Magento_Downloadable_Model_Link_Purchased setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Magento_Downloadable_Model_Link_Purchased setUpdatedAt(string $value)
 * @method int getCustomerId()
 * @method Magento_Downloadable_Model_Link_Purchased setCustomerId(int $value)
 * @method string getProductName()
 * @method Magento_Downloadable_Model_Link_Purchased setProductName(string $value)
 * @method string getProductSku()
 * @method Magento_Downloadable_Model_Link_Purchased setProductSku(string $value)
 * @method string getLinkSectionTitle()
 * @method Magento_Downloadable_Model_Link_Purchased setLinkSectionTitle(string $value)
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Model_Link_Purchased extends Magento_Core_Model_Abstract
{
    /**
     * Enter description here...
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Downloadable_Model_Resource_Link_Purchased');
        parent::_construct();
    }

    /**
     * Check order id
     *
     * @return Magento_Core_Model_Abstract
     */
    public function _beforeSave()
    {
        if (null == $this->getOrderId()) {
            throw new Exception(
                __('Order id cannot be null'));
        }
        return parent::_beforeSave();
    }

}
