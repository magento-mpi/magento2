<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product attribute api
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Api_V2 extends Magento_Catalog_Model_Product_Attribute_Api
{
    /**
     * Api data
     *
     * @var Magento_Api_Helper_Data
     */
    protected $_apiData = null;

    /**
     * @param Magento_Catalog_Helper_Product $catalogProduct
     * @param Magento_Api_Helper_Data $apiData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Cache_FrontendInterface $attributeLabelCache
     */
    public function __construct(
        Magento_Catalog_Helper_Product $catalogProduct,
        Magento_Api_Helper_Data $apiData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Cache_FrontendInterface $attributeLabelCache
    ) {
        $this->_apiData = $apiData;
        parent::__construct($catalogProduct, $catalogData, $attributeLabelCache);
    }

    /**
     * Create new product attribute
     *
     * @param array $data input data
     * @return integer
     */
    public function create($data)
    {
        $helper = $this->_apiData;
        $helper->v2AssociativeArrayUnpacker($data);
        $this->_apiData->toArray($data);
        return parent::create($data);
    }

    /**
     * Update product attribute
     *
     * @param string|integer $attribute attribute code or ID
     * @param array $data
     * @return boolean
     */
    public function update($attribute, $data)
    {
        $helper = $this->_apiData;
        $helper->v2AssociativeArrayUnpacker($data);
        $this->_apiData->toArray($data);
        return parent::update($attribute, $data);
    }

    /**
     * Add option to select or multiselect attribute
     *
     * @param  integer|string $attribute attribute ID or code
     * @param  array $data
     * @return bool
     */
    public function addOption($attribute, $data)
    {
        $this->_apiData->toArray($data);
        return parent::addOption($attribute, $data);
    }

    /**
     * Get full information about attribute with list of options
     *
     * @param integer|string $attribute attribute ID or code
     * @return array
     */
    public function info($attribute)
    {
        $result = parent::info($attribute);
        if (!empty($result['additional_fields'])){
            $keys = array_keys($result['additional_fields']);
            foreach ($keys as $key ) {
                $result['additional_fields'][] = array(
                    'key' => $key,
                    'value' => $result['additional_fields'][$key]
                );
                unset($result['additional_fields'][$key]);
            }
        }
        return $result;
    }
}
