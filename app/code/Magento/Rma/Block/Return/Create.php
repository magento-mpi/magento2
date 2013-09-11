<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Return;

class Create extends \Magento\Rma\Block\Form
{
    public function _construct()
    {
        $order = \Mage::registry('current_order');
        if (!$order) {
            return;
        }
        $this->setOrder($order);

        $items = \Mage::helper('Magento\Rma\Helper\Data')->getOrderItems($order);
        $this->setItems($items);

        $session = \Mage::getSingleton('Magento\Core\Model\Session');
        $formData = $session->getRmaFormData(true);
        if (!empty($formData)) {
            $data = new \Magento\Object();
            $data->addData($formData);
            $this->setFormData($data);
        }
        $errorKeys = $session->getRmaErrorKeys(true);
        if (!empty($errorKeys)) {
            $data = new \Magento\Object();
            $data->addData($errorKeys);
            $this->setErrorKeys($data);
        }
    }

    /**
     * Retrieves item qty available for return
     *
     * @param  $item | \Magento\Sales\Model\Order\Item
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
        return \Mage::getUrl('sales/order/history');
    }


    /**
     * Prepare rma item attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        /* @var $itemModel */
        $itemModel = \Mage::getModel('Magento\Rma\Model\Item');

        /* @var $itemForm \Magento\Rma\Model\Item\Form */
        $itemForm = \Mage::getModel('Magento\Rma\Model\Item\Form');
        $itemForm->setFormCode('default')
            ->setStore($this->getStore())
            ->setEntity($itemModel);

        // prepare item attributes to show
        $attributes = array();

        // add system required attributes
        foreach ($itemForm->getSystemAttributes() as $attribute) {
            /* @var $attribute \Magento\Rma\Model\Item\Attribute */
            if ($attribute->getIsVisible()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        // add user defined attributes
        foreach ($itemForm->getUserAttributes() as $attribute) {
            /* @var $attribute \Magento\Rma\Model\Item\Attribute */
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
     * @param $a \Magento\Rma\Model\Item\Attribute
     * @param $b \Magento\Rma\Model\Item\Attribute
     *
     * @return int
     */
    protected function _compareSortOrder(\Magento\Rma\Model\Item\Attribute $a, \Magento\Rma\Model\Item\Attribute $b)
    {
        $diff = $a->getSortOrder() - $b->getSortOrder();
        return $diff ? ($diff > 0 ? 1 : -1) : 0;
    }
}
