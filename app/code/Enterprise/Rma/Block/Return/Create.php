<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Rma_Block_Return_Create extends Enterprise_Rma_Block_Form
{
    public function _construct()
    {
        $order = Mage::registry('current_order');
        if (!$order) {
            return;
        }
        $this->setOrder($order);

        $items = Mage::helper('Enterprise_Rma_Helper_Data')->getOrderItems($order);
        $this->setItems($items);

        $session = Mage::getSingleton('Magento_Core_Model_Session');
        $formData = $session->getRmaFormData(true);
        if (!empty($formData)) {
            $data = new Magento_Object();
            $data->addData($formData);
            $this->setFormData($data);
        }
        $errorKeys = $session->getRmaErrorKeys(true);
        if (!empty($errorKeys)) {
            $data = new Magento_Object();
            $data->addData($errorKeys);
            $this->setErrorKeys($data);
        }
    }

    /**
     * Retrieves item qty available for return
     *
     * @param  $item | Magento_Sales_Model_Order_Item
     * @return int
     */
    public function getAvailableQty($item)
    {
        $return = $item->getAvailableQty();
        if (!$item->getIsQtyDecimal()) {
            $return = intval($return);
        }
        return $return;
    }



    public function getBackUrl()
    {
        return Mage::getUrl('sales/order/history');
    }


    /**
     * Prepare rma item attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        /* @var $itemModel */
        $itemModel = Mage::getModel('Enterprise_Rma_Model_Item');

        /* @var $itemForm Enterprise_Rma_Model_Item_Form */
        $itemForm = Mage::getModel('Enterprise_Rma_Model_Item_Form');
        $itemForm->setFormCode('default')
            ->setStore($this->getStore())
            ->setEntity($itemModel);

        // prepare item attributes to show
        $attributes = array();

        // add system required attributes
        foreach ($itemForm->getSystemAttributes() as $attribute) {
            /* @var $attribute Enterprise_Rma_Model_Item_Attribute */
            if ($attribute->getIsVisible()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        // add user defined attributes
        foreach ($itemForm->getUserAttributes() as $attribute) {
            /* @var $attribute Enterprise_Rma_Model_Item_Attribute */
            if ($attribute->getIsVisible()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        uasort($attributes, array($this, '_compareSortOrder'));

        return $attributes;
    }

    /**
     * Retrieves Contact Email Address on error
     *
     * @return string
     */
    public function getContactEmail()
    {
        $data   = $this->getFormData();
        $email  = '';

        if ($data) {
            $email = $this->escapeHtml($data->getCustomerCustomEmail());
        }
        return $email;
    }

    /**
     * Compares sort order of attributes, returns -1, 0 or 1 if $a sort
     * order is less, equal or greater than $b sort order respectively.
     *
     * @param $a Enterprise_Rma_Model_Item_Attribute
     * @param $b Enterprise_Rma_Model_Item_Attribute
     *
     * @return int
     */
    protected function _compareSortOrder(Enterprise_Rma_Model_Item_Attribute $a, Enterprise_Rma_Model_Item_Attribute $b)
    {
        $diff = $a->getSortOrder() - $b->getSortOrder();
        return $diff ? ($diff > 0 ? 1 : -1) : 0;
    }
}
