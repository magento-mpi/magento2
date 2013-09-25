<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Weee_Model_Attribute_Backend_Weee_Tax extends Magento_Catalog_Model_Product_Attribute_Backend_Price
{
    /**
     * @var Magento_Weee_Model_Resource_Attribute_Backend_Weee_Tax
     */
    protected $_attributeTax;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Directory_Helper_Data
     */
    protected $_directoryHelper;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Directory_Helper_Data $directoryHelper
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Model_Config $config
     * @param Magento_Directory_Model_CurrencyFactory $currencyFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Weee_Model_Resource_Attribute_Backend_Weee_Tax $attributeTax
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Directory_Helper_Data $directoryHelper,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Model_Config $config,
        Magento_Directory_Model_CurrencyFactory $currencyFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Weee_Model_Resource_Attribute_Backend_Weee_Tax $attributeTax
    ) {
        $this->_directoryHelper = $directoryHelper;
        $this->_storeManager = $storeManager;
        $this->_attributeTax = $attributeTax;
        parent::__construct($logger, $currencyFactory, $storeManager, $catalogData, $config);
    }

    public static function getBackendModelName()
    {
        return 'Magento_Weee_Model_Attribute_Backend_Weee_Tax';
    }

    /**
     * Validate data
     *
     * @param   Magento_Catalog_Model_Product $object
     * @return  this
     */
    public function validate($object)
    {
        $taxes = $object->getData($this->getAttribute()->getName());
        if (empty($taxes)) {
            return $this;
        }
        $dup = array();

        foreach ($taxes as $tax) {
            if (!empty($tax['delete'])) {
                continue;
            }

            $state = isset($tax['state']) ? $tax['state'] : '*';
            $key1 = implode('-', array($tax['website_id'], $tax['country'], $state));

            if (!empty($dup[$key1])) {
                throw new Magento_Core_Exception(
                    __('We found a duplicate website, country, and state tax.')
                );
            }
            $dup[$key1] = 1;
        }
        return $this;
    }

    /**
     * Assign WEEE taxes to product data
     *
     * @param   Magento_Catalog_Model_Product $object
     * @return  Magento_Catalog_Model_Product_Attribute_Backend_Weee
     */
    public function afterLoad($object)
    {
        $data = $this->_attributeTax->loadProductData($object, $this->getAttribute());

        foreach ($data as $i=>$row) {
            if ($data[$i]['website_id'] == 0) {
                $rate = $this->_storeManager->getStore()
                    ->getBaseCurrency()->getRate($this->_directoryHelper->getBaseCurrencyCode());
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

    public function afterSave($object)
    {
        $orig = $object->getOrigData($this->getAttribute()->getName());
        $current = $object->getData($this->getAttribute()->getName());
        if ($orig == $current) {
            return $this;
        }

        $this->_attributeTax->deleteProductData($object, $this->getAttribute());
        $taxes = $object->getData($this->getAttribute()->getName());

        if (!is_array($taxes)) {
            return $this;
        }

        foreach ($taxes as $tax) {
            if (empty($tax['price']) || empty($tax['country']) || !empty($tax['delete'])) {
                continue;
            }

            if (isset($tax['state']) && $tax['state']) {
                $state = $tax['state'];
            } else {
                $state = '*';
            }

            $data = array();
            $data['website_id']   = $tax['website_id'];
            $data['country']      = $tax['country'];
            $data['state']        = $state;
            $data['value']        = $tax['price'];
            $data['attribute_id'] = $this->getAttribute()->getId();

            $this->_attributeTax->insertProductData($object, $data);
        }

        return $this;
    }

    public function afterDelete($object)
    {
        $this->_attributeTax->deleteProductData($object, $this->getAttribute());
        return $this;
    }

    public function getTable()
    {
        return $this->_attributeTax->getTable('weee_tax');
    }
}

