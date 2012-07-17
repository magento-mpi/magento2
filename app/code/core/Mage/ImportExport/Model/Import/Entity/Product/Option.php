<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Entity class which provide possibility to import product custom options
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_ImportExport_Model_Import_Entity_Product_Option extends Mage_ImportExport_Model_Import_Entity_Abstract
{
    /**
     * All stores code-ID pairs
     *
     * @var array
     */
    protected $_storeCodeToId = array();

    /**
     * List of products sku-ID pairs
     *
     * @var array
     */
    protected $_productsSkuToId = array();

    /**
     * Core resource model
     *
     * @var Mage_Core_Model_Resource
     */
    protected $_coreResource;

    /**
     * Instance of import/export resource helper
     *
     * @var Mage_ImportExport_Model_Resource_Helper_Mysql4
     */
    protected $_resourceHelper;

    /**
     * Flag for global prices property
     *
     * @var bool
     */
    protected $_isPriceGlobal;

    /**
     * List of specific custom option types
     *
     * @var array
     */
    protected $_specificTypes = array(
        'date'      => array('price', 'sku'),
        'date_time' => array('price', 'sku'),
        'time'      => array('price', 'sku'),
        'field'     => array('price', 'sku', 'max_characters'),
        'area'      => array('price', 'sku', 'max_characters'),
        'drop_down' => true,
        'radio'     => true,
        'checkbox'  => true,
        'multiple'  => true
    );

    /**
     * Keep product id value for every row which will be imported
     *
     * @var int
     */
    protected $_rowProductId;

    /**
     * Keep store id value for every row which will be imported
     *
     * @var int
     */
    protected $_rowStoreId;

    /**
     * Keep information about row status
     *
     * @var int
     */
    protected $_rowIsMain;

    /**
     * Keep type value for every row which will be imported
     *
     * @var int
     */
    protected $_rowType;

    /**
     * Product model instance
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_productModel;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->_dataSourceModel = Mage_ImportExport_Model_Import::getDataSourceModel();
        $this->_coreResource = isset($data['core_resource_model']) ? $data['core_resource_model']
            : Mage::getSingleton('Mage_Core_Model_Resource');
        $this->_connection = $this->_coreResource->getConnection('write');
        $this->_resourceHelper = isset($data['resource_helper']) ? $data['resource_helper']
            : Mage::getResourceHelper('Mage_ImportExport');
        $this->_productModel = isset($data['product_model']) ? $data['product_model']
            : Mage::getModel('Mage_Catalog_Model_Product');

        if (isset($data['is_price_global'])) {
            $this->_isPriceGlobal = $data['is_price_global'];
        } else {
            /** @var $catalogHelper Mage_Catalog_Helper_Data */
            $catalogHelper = Mage::helper('Mage_Catalog_Helper_Data');
            $this->_isPriceGlobal = $catalogHelper->isPriceGlobal();
        }

        if (isset($data['stores'])) {
            $this->_storeCodeToId = $data['stores'];
        } else {
            /** @var $store Mage_Core_Model_Store */
            foreach (Mage::app()->getStores() as $store) {
                $this->_storeCodeToId[$store->getCode()] = $store->getId();
            }
        }
    }

    /**
     * Load data of existed products
     *
     * @return Mage_ImportExport_Model_Import_Entity_Product_Option
     */
    protected function _initProductsSku()
    {
        if (!$this->_productsSkuToId) {
            $columns = array('entity_id', 'sku');
            foreach ($this->_productModel->getProductEntitiesInfo($columns) as $product) {
                $this->_productsSkuToId[$product['sku']] = $product['entity_id'];
            }
        }

        return $this;
    }

    /**
     * Imported entity type code getter
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'product_options';
    }

    /**
     * Validate data row
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return bool
     */
    public function validateRow(array $rowData, $rowNumber)
    {
        return true;
    }

    /**
     * Import data rows
     *
     * @return boolean
     */
    protected function _importData()
    {
        $this->_initProductsSku();

        $optionTable    = $this->_coreResource->getTableName('catalog_product_option');
        $typeValueTable = $this->_coreResource->getTableName('catalog_product_option_type_value');
        $nextOptionId   = $this->_resourceHelper->getNextAutoincrement($optionTable);
        $nextValueId    = $this->_resourceHelper->getNextAutoincrement($typeValueTable);
        $prevOptionId   = null;

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $products   = array();
            $options    = array();
            $titles     = array();
            $prices     = array();
            $typeValues = array();
            $typePrices = array();
            $typeTitles = array();

            foreach ($bunch as $rowNumber => $rowData) {
                if (!$this->isRowAllowedToImport($rowData, $rowNumber)) {
                    continue;
                }

                $commonData = $this->_getRequiredData($rowData);
                if (!$commonData) {
                    continue;
                }
                list($productId, $storeId, $optionType, $rowIsMain) = $commonData;

                if ($rowIsMain) {
                    $options[] = $this->_getOptionData($rowData, $productId, $nextOptionId, $optionType);

                    if (!$this->_isRowHasSpecificType($optionType)) {
                        $prices[] = $this->_getPriceData($rowData, $nextOptionId, $optionType);
                    }

                    if (!isset($products[$productId])) {
                        $products[$productId] = $this->_getProductData($rowData, $productId);
                    }

                    $prevOptionId = $nextOptionId++;
                }

                if ($this->_isRowHasSpecificType($optionType) && $prevOptionId) {
                    $specificTypeData = $this->_getSpecificTypeData($rowData, $nextValueId);
                    if ($specificTypeData) {
                        $typeValues[$prevOptionId][] = $specificTypeData['value'];

                        // ensure default title is set
                        if (!isset($typeTitles[$nextValueId][0])) {
                            $typeTitles[$nextValueId][0] = $specificTypeData['title'];
                        }
                        $typeTitles[$nextValueId][$storeId] = $specificTypeData['title'];;

                        if ($specificTypeData['price']) {
                            if ($this->_isPriceGlobal) {
                                $typePrices[$nextValueId][0] = $specificTypeData['price'];
                            } else {
                                // ensure default price is set
                                if (!isset($typePrices[$nextValueId][0])) {
                                    $typePrices[$nextValueId][0] = $specificTypeData['price'];
                                }
                                $typePrices[$nextValueId][$storeId] = $specificTypeData['price'];
                            }
                        }

                        $nextValueId++;
                    }
                }

                if (!empty($rowData['_custom_option_title'])) {
                    if (!isset($titles[$prevOptionId][0])) { // ensure default title is set
                        $titles[$prevOptionId][0] = $rowData['_custom_option_title'];
                    }
                    $titles[$prevOptionId][$storeId] = $rowData['_custom_option_title'];
                }
            }

            // Save prepared custom options data !!!
            if ($this->getBehavior() != Mage_ImportExport_Model_Import::BEHAVIOR_APPEND) {
                $this->_deleteEntities(array_keys($products));
            }

            $isOptionsSaved = $this->_saveOptions($options, $titles, $typeValues);
            if (!$isOptionsSaved) {
                continue;
            }

            $this->_saveTitles($titles)
                ->_savePrices($prices)
                ->_saveSpecificTypeValues($typeValues)
                ->_saveSpecificTypePrices($typePrices)
                ->_saveSpecificTypeTitles($typeTitles)
                ->_updateProducts($products);
        }

        return true;
    }

    /**
     * Get required params for future import
     *
     * @param array $rowData
     * @return array|bool
     */
    protected function _getRequiredData(array $rowData)
    {
        if (isset($rowData[Mage_ImportExport_Model_Import_Entity_Product::COL_SKU])) {
            $this->_rowProductId =
                $this->_productsSkuToId[$rowData[Mage_ImportExport_Model_Import_Entity_Product::COL_SKU]];
        } elseif (!isset($this->_rowProductId)) {
            return false;
        }
        // Init store
        if (!empty($rowData['_custom_option_store'])) {
            if (!isset($this->_storeCodeToId[$rowData['_custom_option_store']])) {
                return false;
            }
            $this->_rowStoreId = $this->_storeCodeToId[$rowData['_custom_option_store']];
        } else {
            $this->_rowStoreId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
        }
        // Init option type and set param which tell that row is main
        if (!empty($rowData['_custom_option_type'])) { // get CO type if its specified
            if (!isset($this->_specificTypes[$rowData['_custom_option_type']])) {
                $this->_rowType = null;
                return false;
            }
            $this->_rowType = $rowData['_custom_option_type'];
            $this->_rowIsMain = true;
        } else {
            if (null === $this->_rowType) {
                return false;
            }
            $this->_rowIsMain = false;
        }

        return array(
            $this->_rowProductId,
            $this->_rowStoreId,
            $this->_rowType,
            $this->_rowIsMain
        );
    }

    /**
     * Check is current row has specific type
     *
     * @param string $type
     * @return bool
     */
    protected function _isRowHasSpecificType($type)
    {
        if (isset($this->_specificTypes[$type])) {
            return $this->_specificTypes[$type] === true;
        }

        return false;
    }

    /**
     * Retrieve product data for future update
     *
     * @param array $rowData
     * @param int $productId
     * @return array
     */
    protected function _getProductData(array $rowData, $productId)
    {
        $productData = array(
            'entity_id'        => $productId,
            'has_options'      => 1,
            'required_options' => 0,
            'updated_at'       => now(),
        );

        if (!empty($rowData['_custom_option_is_required'])) {
            $productData['required_options'] = 1;
        }

        return $productData;
    }

    /**
     * Retrieve option data
     *
     * @param array $rowData
     * @param int $productId
     * @param int $optionId
     * @param string $type
     * @return array
     */
    protected function _getOptionData(array $rowData, $productId, $optionId, $type)
    {
        $optionData = array(
            'option_id'      => $optionId,
            'sku'            => '',
            'max_characters' => 0,
            'file_extension' => null,
            'image_size_x'   => 0,
            'image_size_y'   => 0,
            'product_id'     => $productId,
            'type'           => $type,
            'is_require'     => empty($rowData['_custom_option_is_required']) ? 0 : 1,
            'sort_order'     => empty($rowData['_custom_option_sort_order'])
                ? 0 : abs($rowData['_custom_option_sort_order'])
        );

        if (!$this->_isRowHasSpecificType($type)) { // simple option may have optional params
            foreach ($this->_specificTypes[$type] as $paramSuffix) {
                if (isset($rowData['_custom_option_' . $paramSuffix])) {
                    $data = $rowData['_custom_option_' . $paramSuffix];

                    if (array_key_exists($paramSuffix, $optionData)) {
                        $optionData[$paramSuffix] = $data;
                    }
                }
            }
        }
        return $optionData;
    }

    /**
     * Retrieve price data
     *
     * @param array $rowData
     * @param int $optionId
     * @param string $type
     * @return array
     */
    protected function _getPriceData(array $rowData, $optionId, $type)
    {
        $priceData = array(
            'option_id'  => $optionId,
            'store_id'   => Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID,
            'price'      => 0,
            'price_type' => 'fixed'
        );

        foreach ($this->_specificTypes[$type] as $paramSuffix) {
            if (isset($rowData['_custom_option_' . $paramSuffix])) {
                $data = $rowData['_custom_option_' . $paramSuffix];

                if ('price' == $paramSuffix) {
                    if ('%' == substr($data, -1)) {
                        $priceData['price_type'] = 'percent';
                    }
                    $priceData['price'] = (float) rtrim($data, '%');
                }
            }
        }

        return $priceData;
    }

    /**
     * Retrieve specific type data
     *
     * @param array $rowData
     * @param int $optionTypeId
     * @return array|bool
     */
    protected function _getSpecificTypeData(array $rowData, $optionTypeId)
    {
        if (!empty($rowData['_custom_option_row_title']) && empty($rowData['_custom_option_store'])) {
            $valueData = array(
                'option_type_id' => $optionTypeId,
                'sort_order'     => empty($rowData['_custom_option_row_sort'])
                    ? 0 : abs($rowData['_custom_option_row_sort']),
                'sku'            => !empty($rowData['_custom_option_row_sku'])
                    ? $rowData['_custom_option_row_sku'] : ''
            );

            $priceData = false;
            if (!empty($rowData['_custom_option_row_price'])) {
                $priceData = array(
                    'price'      => (float) rtrim($rowData['_custom_option_row_price'], '%'),
                    'price_type' => 'fixed'
                );
                if ('%' == substr($rowData['_custom_option_row_price'], -1)) {
                    $priceData['price_type'] = 'percent';
                }
            }

            return array(
                'value' => $valueData,
                'title' => $rowData['_custom_option_row_title'],
                'price' => $priceData
            );
        }

        return false;
    }

    /**
     * Delete custom options for products
     *
     * @param array $productIds
     * @return Mage_ImportExport_Model_Import_Entity_Product_Option
     */
    protected function _deleteEntities(array $productIds)
    {
        $this->_connection->delete(
            $this->_coreResource->getTableName('catalog_product_option'),
            $this->_connection->quoteInto('product_id IN (?)', $productIds)
        );

        return $this;
    }

    /**
     * Save custom options main info
     *
     * @param array $options options data
     * @param array $titles option titles data
     * @param array $typeValues option type values
     * @return bool
     */
    protected function _saveOptions(array $options, array $titles, array $typeValues)
    {
        // if complex options does not contain values - ignore them
        foreach ($options as $key => $optionData) {
            $optionId = $optionData['option_id'];
            $optionType = $optionData['type'];
            if ($this->_specificTypes[$optionType] === true && !isset($typeValues[$optionId])) {
                unset($options[$key], $titles[$optionId]);
            }
        }

        if ($options) {
            $this->_connection->insertMultiple($this->_coreResource->getTableName('catalog_product_option'), $options);
        } else {
            return false;
        }

        return true;
    }

    /**
     * Save custom option titles
     *
     * @param array $titles option titles data
     * @return Mage_ImportExport_Model_Import_Entity_Product_Option
     */
    protected function _saveTitles(array $titles)
    {
        $titleRows = array();
        foreach ($titles as $optionId => $storeInfo) {
            foreach ($storeInfo as $storeId => $title) {
                $titleRows[] = array(
                    'option_id' => $optionId,
                    'store_id'  => $storeId,
                    'title'     => $title
                );
            }
        }
        if ($titleRows) {
            $this->_connection->insertOnDuplicate(
                $this->_coreResource->getTableName('catalog_product_option_title'),
                $titleRows,
                array('title')
            );
        }

        return $this;
    }

    /**
     * Save custom option prices
     *
     * @param array $prices option prices data
     * @return Mage_ImportExport_Model_Import_Entity_Product_Option
     */
    protected function _savePrices(array $prices)
    {
        if ($prices) {
            $this->_connection->insertOnDuplicate(
                $this->_coreResource->getTableName('catalog_product_option_price'),
                $prices,
                array('price', 'price_type')
            );
        }

        return $this;
    }

    /**
     * Save custom option type values
     *
     * @param array $typeValues option type values
     * @return Mage_ImportExport_Model_Import_Entity_Product_Option
     */
    protected function _saveSpecificTypeValues(array $typeValues)
    {
        $typeValueRows = array();
        foreach ($typeValues as $optionId => $optionInfo) {
            foreach ($optionInfo as $row) {
                $row['option_id'] = $optionId;
                $typeValueRows[]  = $row;
            }
        }
        if ($typeValueRows) {
            $this->_connection->insertMultiple(
                $this->_coreResource->getTableName('catalog_product_option_type_value'),
                $typeValueRows
            );
        }

        return $this;
    }

    /**
     * Save custom option type prices
     *
     * @param array $typePrices option type prices
     * @return Mage_ImportExport_Model_Import_Entity_Product_Option
     */
    protected function _saveSpecificTypePrices(array $typePrices)
    {
        $optionTypePriceRows = array();
        foreach ($typePrices as $optionTypeId => $storesData) {
            foreach ($storesData as $storeId => $row) {
                $row['option_type_id'] = $optionTypeId;
                $row['store_id']       = $storeId;
                $optionTypePriceRows[] = $row;
            }
        }
        if ($optionTypePriceRows) {
            $this->_connection->insertOnDuplicate(
                $this->_coreResource->getTableName('catalog_product_option_type_price'),
                $optionTypePriceRows,
                array('price', 'price_type')
            );
        }

        return $this;
    }

    /**
     * Save custom option type titles
     *
     * @param array $typeTitles option type titles
     * @return Mage_ImportExport_Model_Import_Entity_Product_Option
     */
    protected function _saveSpecificTypeTitles(array $typeTitles)
    {
        $optionTypeTitleRows = array();
        foreach ($typeTitles as $optionTypeId => $storesData) {
            foreach ($storesData as $storeId => $title) {
                $optionTypeTitleRows[] = array(
                    'option_type_id' => $optionTypeId,
                    'store_id'       => $storeId,
                    'title'          => $title
                );
            }
        }
        if ($optionTypeTitleRows) {
            $this->_connection->insertOnDuplicate(
                $this->_coreResource->getTableName('catalog_product_option_type_title'),
                $optionTypeTitleRows,
                array('title')
            );
        }

        return $this;
    }

    /**
     * Update product data which related to custom options information
     *
     * @param array $data product data which will be updated
     * @return Mage_ImportExport_Model_Import_Entity_Product_Option
     */
    protected function _updateProducts(array $data)
    {
        if ($data) {
            $this->_connection->insertOnDuplicate(
                $this->_coreResource->getTableName('catalog_product_entity'),
                $data,
                array('has_options', 'required_options', 'updated_at')
            );
        }

        return $this;
    }
}
