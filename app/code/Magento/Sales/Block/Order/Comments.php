<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Sales_Block_Order_Comments extends Magento_Core_Block_Template
{
    /**
     * Current entity (model instance) with getCommentsCollection() method
     *
     * @var Magento_Sales_Model_Abstract
     */
    protected $_entity;

    /**
     * Currect comments collection
     *
     * @var Magento_Sales_Model_Resource_Order_Comment_Collection_Abstract
     */
    protected $_commentCollection;

    /**
     * Sets comments parent model instance
     *
     * @param Magento_Sales_Model_Abstract
     * @return Magento_Sales_Block_Order_Comments
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
        $this->_commentCollection = null; // Changing model and resource model can lead to change of comment collection
        return $this;
    }

    /**
     * Gets comments parent model instance
     *
     * @return Magento_Sales_Model_Abstract
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Initialize model comments and return comment collection
     *
     * @return Magento_Sales_Model_Resource_Order_Comment_Collection_Abstract
     */
    public function getComments()
    {
        if (is_null($this->_commentCollection)) {
            $entity = $this->getEntity();
            if ($entity instanceof Magento_Sales_Model_Order_Invoice) {
                $collectionClass = 'Magento_Sales_Model_Resource_Order_Invoice_Comment_Collection';
            } else if ($entity instanceof Magento_Sales_Model_Order_Creditmemo) {
                $collectionClass = 'Magento_Sales_Model_Resource_Order_Creditmemo_Comment_Collection';
            } else if ($entity instanceof Magento_Sales_Model_Order_Shipment) {
                $collectionClass = 'Magento_Sales_Model_Resource_Order_Shipment_Comment_Collection';
            } else {
                Mage::throwException(__('We found an invalid entity model.'));
            }

            $this->_commentCollection = Mage::getResourceModel($collectionClass);
            $this->_commentCollection->setParentFilter($entity)
               ->setCreatedAtOrder()
               ->addVisibleOnFrontFilter();
        }

        return $this->_commentCollection;
    }

    /**
     * Returns whether there are comments to show on frontend
     *
     * @return bool
     */
    public function hasComments()
    {
        return $this->getComments()->count() > 0;
    }
}
