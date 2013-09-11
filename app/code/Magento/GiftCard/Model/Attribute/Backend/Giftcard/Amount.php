<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Model\Attribute\Backend\Giftcard;

class Amount
    extends \Magento\Catalog\Model\Product\Attribute\Backend\Price
{
    /**
     * Retrieve resource model
     *
     * @return Magento_GiftCard_Model_Resource_Attribute_Backend_Giftcard_Amounts
     */
    protected function _getResource()
    {
        return \Mage::getResourceSingleton('\Magento\GiftCard\Model\Resource\Attribute\Backend\Giftcard\Amount');
    }

    /**
     * Validate data
     *
     * @param   \Magento\Catalog\Model\Product $object
     * @return  Magento_GiftCard_Model_Attribute_Backend_Giftcard_Amounts
     */
    public function validate($object)
    {
        $rows = $object->getData($this->getAttribute()->getName());
        if (empty($rows)) {
            return $this;
        }
        $dup = array();

        foreach ($rows as $row) {
            if (!isset($row['price']) || !empty($row['delete'])) {
                continue;
            }

            $key1 = implode('-', array($row['website_id'], $row['price']));

            if (!empty($dup[$key1])) {
                \Mage::throwException(
                    __('Duplicate amount found.')
                );
            }
            $dup[$key1] = 1;
        }
        return $this;
    }

    /**
     * Assign amounts to product data
     *
     * @param   \Magento\Catalog\Model\Product $object
     * @return  Magento_GiftCard_Model_Attribute_Backend_Giftcard_Amounts
     */
    public function afterLoad($object)
    {
        $data = $this->_getResource()->loadProductData($object, $this->getAttribute());

        foreach ($data as $i=>$row) {
            if ($data[$i]['website_id'] == 0) {
                $rate = \Mage::app()->getStore()->getBaseCurrency()->getRate(\Mage::app()->getBaseCurrencyCode());
                if ($rate) {
                    $data[$i]['website_value'] = $data[$i]['value']/$rate;
                } else {
                    unset($data[$i]);
                }
            } else {
                $data[$i]['website_value'] = $data[$i]['value'];
            }

        }
        $object->setData($this->getAttribute()->getName(), $data);
        return $this;
    }

    /**
     * Save amounts data
     *
     * @param \Magento\Catalog\Model\Product $object
     * @return Magento_GiftCard_Model_Attribute_Backend_Giftcard_Amounts
     */
    public function afterSave($object)
    {
        $orig = $object->getOrigData($this->getAttribute()->getName());
        $current = $object->getData($this->getAttribute()->getName());
        if ($orig == $current) {
            return $this;
        }

        $this->_getResource()->deleteProductData($object, $this->getAttribute());
        $rows = $object->getData($this->getAttribute()->getName());

        if (!is_array($rows)) {
            return $this;
        }

        foreach ($rows as $row) {
            // Handle the case when model is saved whithout data received from user
            if (((!isset($row['price']) || empty($row['price'])) && !isset($row['value']))
                || !empty($row['delete'])
            ) {
                continue;
            }

            $data = array();
            $data['website_id']   = $row['website_id'];
            $data['value']        = (isset($row['price'])) ? $row['price'] : $row['value'];
            $data['attribute_id'] = $this->getAttribute()->getId();

            $this->_getResource()->insertProductData($object, $data);
        }

        return $this;
    }

    /**
     * Delete amounts data
     *
     * @param \Magento\Catalog\Model\Product $object
     * @return Magento_GiftCard_Model_Attribute_Backend_Giftcard_Amounts
     */
    public function afterDelete($object)
    {
        $this->_getResource()->deleteProductData($object, $this->getAttribute());
        return $this;
    }

    /**
     * Retreive storage table
     *
     * @return string
     */
/*
    public function getTable()
    {
        return $this->_getResource()->getMainTable();
    }
*/
}
