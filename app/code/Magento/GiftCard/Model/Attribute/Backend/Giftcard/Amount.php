<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Model_Attribute_Backend_Giftcard_Amount
    extends Magento_Catalog_Model_Product_Attribute_Backend_Price
{
    /**
     * Giftcard amount backend resource model
     *
     * @var Magento_GiftCard_Model_Resource_Attribute_Backend_Giftcard_Amount
     */
    protected $_amountResource;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Directory helper
     *
     * @var Magento_Directory_Helper_Data
     */
    protected $_directoryHelper;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Directory_Helper_Data $directoryHelper
     * @param Magento_GiftCard_Model_Resource_Attribute_Backend_Giftcard_Amount $amountResource
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Model_Logger $logger
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Directory_Helper_Data $directoryHelper,
        Magento_GiftCard_Model_Resource_Attribute_Backend_Giftcard_Amount $amountResource,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Model_Logger $logger
    ) {
        $this->_storeManager = $storeManager;
        $this->_directoryHelper = $directoryHelper;
        $this->_amountResource = $amountResource;
        parent::__construct($catalogData, $logger);
    }

    /**
     * Validate data
     *
     * @param   Magento_Catalog_Model_Product $object
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
                throw new Magento_Core_Exception(
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
     * @param   Magento_Catalog_Model_Product $object
     * @return  Magento_GiftCard_Model_Attribute_Backend_Giftcard_Amounts
     */
    public function afterLoad($object)
    {
        $data = $this->_amountResource->loadProductData($object, $this->getAttribute());

        foreach ($data as $i=>$row) {
            if ($data[$i]['website_id'] == 0) {
                $rate = $this->_storeManager->getStore()->getBaseCurrency()
                    ->getRate($this->_directoryHelper->getBaseCurrencyCode());
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
     * @param Magento_Catalog_Model_Product $object
     * @return Magento_GiftCard_Model_Attribute_Backend_Giftcard_Amounts
     */
    public function afterSave($object)
    {
        $orig = $object->getOrigData($this->getAttribute()->getName());
        $current = $object->getData($this->getAttribute()->getName());
        if ($orig == $current) {
            return $this;
        }

        $this->_amountResource->deleteProductData($object, $this->getAttribute());
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

            $this->_amountResource->insertProductData($object, $data);
        }

        return $this;
    }

    /**
     * Delete amounts data
     *
     * @param Magento_Catalog_Model_Product $object
     * @return Magento_GiftCard_Model_Attribute_Backend_Giftcard_Amounts
     */
    public function afterDelete($object)
    {
        $this->_amountResource->deleteProductData($object, $this->getAttribute());
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
        return $this->_amountResource->getMainTable();
    }
*/
}
