<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @method Magento_Sales_Model_Resource_Order_Creditmemo_Comment _getResource()
 * @method Magento_Sales_Model_Resource_Order_Creditmemo_Comment getResource()
 * @method int getParentId()
 * @method Magento_Sales_Model_Order_Creditmemo_Comment setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method Magento_Sales_Model_Order_Creditmemo_Comment setIsCustomerNotified(int $value)
 * @method int getIsVisibleOnFront()
 * @method Magento_Sales_Model_Order_Creditmemo_Comment setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method Magento_Sales_Model_Order_Creditmemo_Comment setComment(string $value)
 * @method string getCreatedAt()
 * @method Magento_Sales_Model_Order_Creditmemo_Comment setCreatedAt(string $value)
 */
class Magento_Sales_Model_Order_Creditmemo_Comment extends Magento_Sales_Model_Abstract
{
    /**
     * Creditmemo instance
     *
     * @var Magento_Sales_Model_Order_Creditmemo
     */
    protected $_creditmemo;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_LocaleInterface $coreLocale
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_LocaleInterface $coreLocale,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context, $registry, $coreLocale, $resource, $resourceCollection, $data
        );
        $this->_storeManager = $storeManager;
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Order_Creditmemo_Comment');
    }

    /**
     * Declare Creditmemo instance
     *
     * @param   Magento_Sales_Model_Order_Creditmemo $creditmemo
     * @return  Magento_Sales_Model_Order_Creditmemo_Comment
     */
    public function setCreditmemo(Magento_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $this->_creditmemo = $creditmemo;
        return $this;
    }

    /**
     * Retrieve Creditmemo instance
     *
     * @return Magento_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_creditmemo;
    }

    /**
     * Get store object
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->getCreditmemo()) {
            return $this->getCreditmemo()->getStore();
        }
        return $this->_storeManager->getStore();
    }

    /**
     * Before object save
     *
     * @return Magento_Sales_Model_Order_Creditmemo_Comment
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
