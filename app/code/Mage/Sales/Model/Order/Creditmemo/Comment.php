<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Comment _getResource()
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Comment getResource()
 * @method int getParentId()
 * @method Mage_Sales_Model_Order_Creditmemo_Comment setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method Mage_Sales_Model_Order_Creditmemo_Comment setIsCustomerNotified(int $value)
 * @method int getIsVisibleOnFront()
 * @method Mage_Sales_Model_Order_Creditmemo_Comment setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method Mage_Sales_Model_Order_Creditmemo_Comment setComment(string $value)
 * @method string getCreatedAt()
 * @method Mage_Sales_Model_Order_Creditmemo_Comment setCreatedAt(string $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Creditmemo_Comment extends Mage_Sales_Model_Abstract
{
    /**
     * Creditmemo instance
     *
     * @var Mage_Sales_Model_Order_Creditmemo
     */
    protected $_creditmemo;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Mage_Sales_Model_Resource_Order_Creditmemo_Comment');
    }

    /**
     * Declare Creditmemo instance
     *
     * @param   Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return  Mage_Sales_Model_Order_Creditmemo_Comment
     */
    public function setCreditmemo(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $this->_creditmemo = $creditmemo;
        return $this;
    }

    /**
     * Retrieve Creditmemo instance
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_creditmemo;
    }

    /**
     * Get store object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->getCreditmemo()) {
            return $this->getCreditmemo()->getStore();
        }
        return Mage::app()->getStore();
    }

    /**
     * Before object save
     *
     * @return Mage_Sales_Model_Order_Creditmemo_Comment
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getCreditmemo()) {
            $this->setParentId($this->getCreditmemo()->getId());
        }

        return $this;
    }
}
