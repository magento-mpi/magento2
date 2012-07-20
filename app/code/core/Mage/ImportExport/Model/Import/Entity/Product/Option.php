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
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @todo Need to divide this class on two because of several responsibilities
 */
class Mage_ImportExport_Model_Import_Entity_Product_Option extends Mage_ImportExport_Model_Import_Entity_Abstract
{
    /**#@+
     * Custom option column names
     */
    const COLUMN_SKU         = 'sku';
    const COLUMN_PREFIX      = '_custom_option_';
    const COLUMN_STORE       = '_custom_option_store';
    const COLUMN_TYPE        = '_custom_option_type';
    const COLUMN_TITLE       = '_custom_option_title';
    const COLUMN_IS_REQUIRED = '_custom_option_is_required';
    const COLUMN_SORT_ORDER  = '_custom_option_sort_order';
    const COLUMN_ROW_TITLE   = '_custom_option_row_title';
    const COLUMN_ROW_PRICE   = '_custom_option_row_price';
    const COLUMN_ROW_SKU     = '_custom_option_row_sku';
    const COLUMN_ROW_SORT    = '_custom_option_row_sort';
    /**#@-*/

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
     * Import/export data helper
     *
     * @var Mage_ImportExport_Helper_Data
     */
    protected $_dataHelper;

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
     * Keep product sku value for every row during validation
     *
     * @var int
     */
    protected $_rowProductSku;

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
     * DB data source model
     *
     * @var Mage_ImportExport_Model_Resource_Import_Data
     */
    protected $_dataSourceModel;

    /**
     * DB connection
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_connection;

    /**
     * Custom options tables
     *
     * @var array
     */
    protected $_tables = array(
        'catalog_product_entity'            => null,
        'catalog_product_option'            => null,
        'catalog_product_option_title'      => null,
        'catalog_product_option_type_title' => null,
        'catalog_product_option_type_value' => null,
        'catalog_product_option_type_price' => null,
        'catalog_product_option_price'      => null,
    );

    /**
     * Parent import product entity
     *
     * @var Mage_ImportExport_Model_Import_Entity_Product
     */
    protected $_productEntity;

    /**
     * Existing custom options data
     *
     * @var array
     */
    protected $_oldCustomOptions;

    /**
     * New custom options data
     *
     * @var array
     */
    protected $_newCustomOptions;

    /**
     * Product options collection
     *
     * @var Mage_Catalog_Model_Resource_Product_Option_Collection
     */
    protected $_optionCollection;

    /**#@+
     * Error codes
     */
    const ERROR_INVALID_TYPE           = 'optionInvalidType';
    const ERROR_EMPTY_TITLE            = 'optionEmptyTitle';
    const ERROR_INVALID_PRICE          = 'optionInvalidPrice';
    const ERROR_INVALID_MAX_CHARACTERS = 'optionInvalidMaxCharacters';
    const ERROR_INVALID_SORT_ORDER     = 'optionInvalidSortOrder';
    const ERROR_EMPTY_ROW_TITLE        = 'optionEmptyRowTitle';
    const ERROR_INVALID_ROW_PRICE      = 'optionInvalidRowPrice';
    const ERROR_INVALID_ROW_SORT       = 'optionInvalidRowSort';
    const ERROR_AMBIGUOUS_NAMES        = 'optionAmbiguousNames';
    const ERROR_AMBIGUOUS_TYPES        = 'optionAmbiguousTypes';
    /**#@-*/

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::ERROR_INVALID_TYPE           => 'Invalid custom option type',
        self::ERROR_EMPTY_TITLE            => 'Empty custom option title',
        self::ERROR_INVALID_PRICE          => 'Invalid custom option price',
        self::ERROR_INVALID_MAX_CHARACTERS => 'Invalid custom option maximum characters value',
        self::ERROR_INVALID_SORT_ORDER     => 'Invalid custom option sort order',
        self::ERROR_EMPTY_ROW_TITLE        => 'Empty custom option value title',
        self::ERROR_INVALID_ROW_PRICE      => 'Invalid custom option value price',
        self::ERROR_INVALID_ROW_SORT       => 'Invalid custom option value sort order',
        self::ERROR_AMBIGUOUS_NAMES        => 'Custom option with this name already declared',
        self::ERROR_AMBIGUOUS_TYPES        => 'Custom option has different type',
    );

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['core_resource'])) {
            $this->_coreResource = $data['core_resource'];
        } else {
            $this->_coreResource = Mage::getSingleton('Mage_Core_Model_Resource');
        }
        if (isset($data['connection'])) {
            $this->_connection = $data['connection'];
        } else {
            $this->_connection = Mage::getSingleton('Mage_Core_Model_Resource')->getConnection('write');
        }
        if (isset($data['resource_helper'])) {
            $this->_resourceHelper = $data['resource_helper'];
        } else {
            $this->_resourceHelper = Mage::getResourceHelper('Mage_ImportExport');
        }
        if (isset($data['data_helper'])) {
            $this->_dataHelper = $data['data_helper'];
        } else {
            $this->_dataHelper = Mage::helper('Mage_ImportExport_Helper_Data');
        }

        if (isset($data['is_price_global'])) {
            $this->_isPriceGlobal = $data['is_price_global'];
        } else {
            /** @var $catalogHelper Mage_Catalog_Helper_Data */
            $catalogHelper = Mage::helper('Mage_Catalog_Helper_Data');
            $this->_isPriceGlobal = $catalogHelper->isPriceGlobal();
        }

        $this->_initSourceEntities($data)
            ->_initTables($data)
            ->_initStores($data);

        foreach ($this->_messageTemplates as $errorCode => $message) {
            $this->_productEntity->addMessageTemplate($errorCode, $message);
        }

        $this->_initProductsSku()
            ->_initOldCustomOptions();
    }

    /**
     * Initialize table names
     *
     * @param array $data
     * @return Mage_ImportExport_Model_Import_Entity_Product_Option
     */
    protected function _initTables(array $data)
    {
        if (isset($data['tables'])) {
            // all the entries of $data['tables'] which have keys that are present in $this->_tables
            $tables = array_intersect_key($data['tables'], $this->_tables);
            $this->_tables = array_merge($this->_tables, $tables);
        }
        foreach ($this->_tables as $key => $value) {
            if ($value == null) {
                $this->_tables[$key] = Mage::getSingleton('Mage_Core_Model_Resource')->getTableName($key);
            }
        }
        return $this;
    }

    /**
     * Initialize stores data
     *
     * @param array $data
     * @return Mage_ImportExport_Model_Import_Entity_Product_Option
     */
    protected function _initStores(array $data)
    {
        if (isset($data['stores'])) {
            $this->_storeCodeToId = $data['stores'];
        } else {
            /** @var $store Mage_Core_Model_Store */
            foreach (Mage::app()->getStores() as $store) {
                $this->_storeCodeToId[$store->getCode()] = $store->getId();
            }
        }
        return $this;
    }

    /**
     * Initialize source entities and collections
     *
     * @param array $data
     * @return Mage_ImportExport_Model_Import_Entity_Product_Option
     */
    protected function _initSourceEntities(array $data)
    {
        if (isset($data['data_source_model'])) {
            $this->_dataSourceModel = $data['data_source_model'];
        } else {
            $this->_dataSourceModel = Mage_ImportExport_Model_Import::getDataSourceModel();
        }
        if (isset($data['product_model'])) {
            $this->_productModel = $data['product_model'];
        } else {
            $this->_productModel = Mage::getModel('Mage_Catalog_Model_Product');
        }
        if (isset($data['option_collection'])) {
            $this->_optionCollection = $data['option_collection'];
        } else {
            $this->_optionCollection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Option_Collection');
        }
        if (isset($data['product_entity'])) {
            $this->_productEntity = $data['product_entity'];
        } else {
            Mage::throwException($this->_dataHelper->__('Option entity must have a parent product entity.'));
        }
        return $this;
    }

    /**
     * Load exiting custom options data
     *
     * @return Mage_ImportExport_Model_Import_Entity_Product_Option
     */
    protected function _initOldCustomOptions()
    {
        if (!$this->_oldCustomOptions) {
            $optionTitleTable = $this->_coreResource->getTableName('catalog_product_option_title');
            $productIds = array_values($this->_productsSkuToId);
            foreach ($this->_storeCodeToId as $storeId) {
                /** @var $collection Mage_Catalog_Model_Resource_Product_Option_Collection */
                $this->_optionCollection->reset();
                $this->_optionCollection->addProductToFilter($productIds);
                $this->_optionCollection->getSelect()
                    ->join(
                    array('option_title' => $optionTitleTable),
                    'option_title.option_id = main_table.option_id',
                    array('title' => 'title')
                )->where('option_title.store_id = ?', $storeId);

                /** @var $customOption Mage_Catalog_Model_Product_Option */
                foreach ($this->_optionCollection as $customOption) {
                    $productId = $customOption->getProductId();
                    if (!isset($this->_oldCustomOptions[$productId])) {
                        $this->_oldCustomOptions[$productId] = array();
                    }
                    $this->_oldCustomOptions[$productId][$storeId][] = array(
                        'id' => $customOption->getId(),
                        'title' => $customOption->getTitle(),
                        'type' => $customOption->getType(),
                    );
                }
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
     * Is option with the same name already exist in imported file
     *
     * @param string $productSku
     * @param string $optionTitle
     * @return bool
     */
    protected function _isNewOptionWithTheSameName($productSku, $optionTitle)
    {
        if (!isset($this->_newCustomOptions[$productSku])) {
            return false;
        }
        foreach ($this->_newCustomOptions[$productSku] as $optionData) {
            if ($optionTitle == $optionData['title']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is there existing custom option with the same name and different type
     *
     * @param string $productSku
     * @param string $optionTitle
     * @param string $optionType
     * @return bool
     */
    protected function _isOldOptionWithDifferentType($productSku, $optionTitle, $optionType)
    {
        if (!isset($this->_productsSkuToId[$productSku])) {
            return false;
        }
        $productId = $this->_productsSkuToId[$productSku];
        if (!isset($this->_oldCustomOptions[$productId])) {
            return false;
        }
        foreach ($this->_oldCustomOptions[$productId] as $storeData) {
            foreach ($storeData as $optionData) {
                if ($optionTitle == $optionData['title'] && $optionType != $optionData['type']) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Validate ambiguous situations:
     * - several custom options has the same name in input file;
     * - custom options with the same name has different data types.
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return bool
     */
    protected function _validateAmbiguousData(array $rowData, $rowNumber)
    {
        $optionType  = $rowData[self::COLUMN_TYPE];
        $optionTitle = $rowData[self::COLUMN_TITLE];

        if (!empty($rowData[Mage_ImportExport_Model_Import_Entity_Product::COL_SKU])) {
            $this->_rowProductSku = $rowData[Mage_ImportExport_Model_Import_Entity_Product::COL_SKU];
        } elseif (empty($this->_rowProductSku)) {
            return false;
        }

        if ($this->_isNewOptionWithTheSameName($this->_rowProductSku, $optionTitle)) {
            $this->_productEntity->addRowError(self::ERROR_AMBIGUOUS_NAMES, $rowNumber);
        } elseif ($this->_isOldOptionWithDifferentType($this->_rowProductSku, $optionTitle, $optionType)) {
            $this->_productEntity->addRowError(self::ERROR_AMBIGUOUS_TYPES, $rowNumber);
        } else {
            return true;
        }

        return false;
    }

    /**
     * Validate main custom option row
     *
     * @param array $rowData
     * @param string $rowNumber
     * @return bool
     */
    protected function _validateMainRow(array $rowData, $rowNumber)
    {
        if (!array_key_exists($rowData[self::COLUMN_TYPE], $this->_specificTypes)) {   // type
            $this->_productEntity->addRowError(self::ERROR_INVALID_TYPE, $rowNumber);
        } elseif (!strlen($rowData[self::COLUMN_TITLE])) {                             // title
            $this->_productEntity->addRowError(self::ERROR_EMPTY_TITLE, $rowNumber);
        } elseif ($this->_validateSpecificTypesParameters($rowData, $rowNumber)) {     // price, max_character
            if (strlen($rowData[self::COLUMN_SORT_ORDER])                              // sort order
                && (!is_numeric($rowData[self::COLUMN_SORT_ORDER]) || $rowData[self::COLUMN_SORT_ORDER] < 0)
            ) {
                $this->addRowError(self::ERROR_INVALID_SORT_ORDER, $rowNumber);
            } elseif ($this->_validateAmbiguousData($rowData, $rowNumber)) {
                // add new column
                if (!isset($this->_newCustomOptions[$this->_rowProductSku])) {
                    $this->_newCustomOptions[$this->_rowProductSku] = array();
                }
                $this->_newCustomOptions[$this->_rowProductSku][] = array(
                    'type'  => $rowData[self::COLUMN_TYPE],
                    'title' => $rowData[self::COLUMN_TITLE],
                );
                return true;
            }
        }
        return false;
    }

    /**
     * Validate secondary custom option row
     *
     * @param array $rowData
     * @param string $rowNumber
     * @return bool
     */
    protected function _validateSecondaryRow(array $rowData, $rowNumber)
    {
        if (!strlen($rowData[self::COLUMN_ROW_TITLE])) {                              // row title
            $this->_productEntity->addRowError(self::ERROR_EMPTY_ROW_TITLE, $rowNumber);
        } else {
            $priceValue = rtrim($rowData[self::COLUMN_ROW_PRICE], '%');
            if (strlen($priceValue) && !is_numeric($priceValue)) {                    // row price
                $this->_productEntity->addRowError(self::ERROR_INVALID_ROW_PRICE, $rowNumber);
            } elseif (strlen($rowData[self::COLUMN_ROW_SORT])                         // row sort
                && (!is_numeric($rowData[self::COLUMN_ROW_SORT]) || $rowData[self::COLUMN_ROW_SORT] < 0)
            ) {
                $this->_productEntity->addRowError(self::ERROR_INVALID_ROW_SORT, $rowNumber);
            } else {
                return true;
            }
        }
        return false;
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
        if (isset($this->_validatedRows[$rowNumber])) {
            return !isset($this->_invalidRows[$rowNumber]);
        }
        $this->_validatedRows[$rowNumber] = true;

        if ($this->_isRowWithCustomOption($rowData)) {
            if ($this->_isMainOptionRow($rowData)) {
                return $this->_validateMainRow($rowData, $rowNumber);
            } else {
                return $this->_validateSecondaryRow($rowData, $rowNumber);
            }
        }
        return false;
    }

    /**
     * Validation of specific types parameters
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return bool
     */
    protected function _validateSpecificTypesParameters(array $rowData, $rowNumber)
    {
        $isValid = true;
        $typeParameters = $this->_specificTypes[$rowData[self::COLUMN_TYPE]];
        if (is_array($typeParameters)) {
            foreach ($typeParameters as $typeParameter) {
                if (!$this->_validateSpecificParameterData($typeParameter, $rowData, $rowNumber)) {
                    $isValid = false;
                }
            }
        }
        return $isValid;
    }

    /**
     * Validate one specific parameter
     *
     * @param string $typeParameter
     * @param array $rowData
     * @param int $rowNumber
     * @return bool
     */
    protected function _validateSpecificParameterData($typeParameter, array $rowData, $rowNumber)
    {
        $fieldName = self::COLUMN_PREFIX . $typeParameter;
        if ($typeParameter == 'price') {
            $priceValue = rtrim($rowData[$fieldName], '%');
            if (strlen($priceValue) && !is_numeric($priceValue)) {
                $this->_productEntity->addRowError(self::ERROR_INVALID_PRICE, $rowNumber);
                return false;
            }
        } elseif ($typeParameter == 'max_characters') {
            if (strlen($rowData[$fieldName])
                && (!is_numeric($rowData[$fieldName]) || $rowData[$fieldName] < 0)
            ) {
                $this->_productEntity->addRowError(self::ERROR_INVALID_MAX_CHARACTERS, $rowNumber);
                return false;
            }
        }
        return true;
    }

    /**
     * Is current row contains custom option information
     *
     * @param array $rowData
     * @return bool
     */
    protected function _isRowWithCustomOption(array $rowData)
    {
        return !empty($rowData[self::COLUMN_TYPE]) || !empty($rowData[self::COLUMN_ROW_TITLE]);
    }

    /**
     * Is current row a main option row (i.e. contains type, title, etc.)
     *
     * @param array $rowData
     * @return bool
     */
    protected function _isMainOptionRow(array $rowData)
    {
        return !empty($rowData[self::COLUMN_TYPE]);
    }

    /**
     * Import data rows
     *
     * @return boolean
     */
    protected function _importData()
    {
        $this->_initProductsSku();

        $nextOptionId = $this->_resourceHelper->getNextAutoincrement($this->_tables['catalog_product_option']);
        $nextValueId  = $this->_resourceHelper->getNextAutoincrement(
            $this->_tables['catalog_product_option_type_value']
        );
        $prevOptionId = 0;

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
                if (!$this->_parseRequiredData($rowData)) {
                    continue;
                }

                $optionData = $this->_collectOptionMainData($rowData, $prevOptionId, $nextOptionId, $products, $prices);
                if ($optionData != null) {
                    $options[] = $optionData;
                }
                $this->_collectOptionTypeData($rowData, $prevOptionId, $nextValueId, $typeValues, $typePrices,
                    $typeTitles
                );
                $this->_collectOptionTitle($rowData, $prevOptionId, $titles);
            }

            // Save prepared custom options data !!!
            if ($this->getBehavior() != Mage_ImportExport_Model_Import::BEHAVIOR_APPEND) {
                $this->_deleteEntities(array_keys($products));
            }

            if ($this->_saveOptions($options, $titles, $typeValues)) {
                $this->_saveTitles($titles)
                    ->_savePrices($prices)
                    ->_saveSpecificTypeValues($typeValues)
                    ->_saveSpecificTypePrices($typePrices)
                    ->_saveSpecificTypeTitles($typeTitles)
                    ->_updateProducts($products);
            }
        }

        return true;
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
     * Collect custom option main data to import
     *
     * @param array $rowData
     * @param int $prevOptionId
     * @param int $nextOptionId
     * @param array $products
     * @param array $prices
     * @return array|null
     */
    protected function _collectOptionMainData(array $rowData, &$prevOptionId, &$nextOptionId, array &$products,
        array &$prices
    ) {
        $optionData = null;

        if ($this->_rowIsMain) {
            $optionData = $this->_getOptionData($rowData, $this->_rowProductId, $nextOptionId, $this->_rowType);

            if (!$this->_isRowHasSpecificType($this->_rowType)) {
                $prices[] = $this->_getPriceData($rowData, $nextOptionId, $this->_rowType);
            }

            if (!isset($products[$this->_rowProductId])) {
                $products[$this->_rowProductId] = $this->_getProductData($rowData, $this->_rowProductId);
            }

            $prevOptionId = $nextOptionId++;
        }

        return $optionData;
    }

    /**
     * Collect custom option type data to import
     *
     * @param array $rowData
     * @param int $prevOptionId
     * @param int $nextValueId
     * @param array $typeValues
     * @param array $typePrices
     * @param array $typeTitles
     */
    protected function _collectOptionTypeData(array $rowData, &$prevOptionId, &$nextValueId, array &$typeValues,
        array &$typePrices, array &$typeTitles
    ) {
        if ($this->_isRowHasSpecificType($this->_rowType) && $prevOptionId) {
            $specificTypeData = $this->_getSpecificTypeData($rowData, $nextValueId);
            if ($specificTypeData) {
                $typeValues[$prevOptionId][] = $specificTypeData['value'];

                // ensure default title is set
                if (!isset($typeTitles[$nextValueId][Mage_Core_Model_App::ADMIN_STORE_ID])) {
                    $typeTitles[$nextValueId][Mage_Core_Model_App::ADMIN_STORE_ID] = $specificTypeData['title'];
                }
                $typeTitles[$nextValueId][$this->_rowStoreId] = $specificTypeData['title'];;

                if ($specificTypeData['price']) {
                    if ($this->_isPriceGlobal) {
                        $typePrices[$nextValueId][Mage_Core_Model_App::ADMIN_STORE_ID] = $specificTypeData['price'];
                    } else {
                        // ensure default price is set
                        if (!isset($typePrices[$nextValueId][Mage_Core_Model_App::ADMIN_STORE_ID])) {
                            $typePrices[$nextValueId][Mage_Core_Model_App::ADMIN_STORE_ID] = $specificTypeData['price'];
                        }
                        $typePrices[$nextValueId][$this->_rowStoreId] = $specificTypeData['price'];
                    }
                }

                $nextValueId++;
            }
        }
    }

    /**
     * Collect custom option title to import
     *
     * @param array $rowData
     * @param int $prevOptionId
     * @param array $titles
     */
    protected function _collectOptionTitle(array $rowData, $prevOptionId, array &$titles)
    {
        if (!empty($rowData[self::COLUMN_TITLE])) {
            if (!isset($titles[$prevOptionId][Mage_Core_Model_App::ADMIN_STORE_ID])) { // ensure default title is set
                $titles[$prevOptionId][Mage_Core_Model_App::ADMIN_STORE_ID] = $rowData[self::COLUMN_TITLE];
            }
            $titles[$prevOptionId][$this->_rowStoreId] = $rowData[self::COLUMN_TITLE];
        }
    }

    /**
     * Parse required data from current row and store to class internal variables some data
     * for underlying dependent rows
     *
     * @param array $rowData
     * @return bool
     */
    protected function _parseRequiredData(array $rowData)
    {
        if ($rowData[self::COLUMN_SKU] != '') {
            $this->_rowProductId = $this->_productsSkuToId[$rowData[self::COLUMN_SKU]];
        } elseif (!isset($this->_rowProductId)) {
            return false;
        }
        // Init store
        if (!empty($rowData[self::COLUMN_STORE])) {
            if (!isset($this->_storeCodeToId[$rowData[self::COLUMN_STORE]])) {
                return false;
            }
            $this->_rowStoreId = $this->_storeCodeToId[$rowData[self::COLUMN_STORE]];
        } else {
            $this->_rowStoreId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
        }
        // Init option type and set param which tell that row is main
        if (!empty($rowData[self::COLUMN_TYPE])) { // get CO type if its specified
            if (!isset($this->_specificTypes[$rowData[self::COLUMN_TYPE]])) {
                $this->_rowType = null;
                return false;
            }
            $this->_rowType = $rowData[self::COLUMN_TYPE];
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
            'updated_at'       => Varien_Date::now(),
        );

        if (!empty($rowData[self::COLUMN_IS_REQUIRED])) {
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
            'is_require'     => empty($rowData[self::COLUMN_IS_REQUIRED]) ? 0 : 1,
            'sort_order'     => empty($rowData[self::COLUMN_SORT_ORDER]) ? 0 : abs($rowData[self::COLUMN_SORT_ORDER])
        );

        if (!$this->_isRowHasSpecificType($type)) { // simple option may have optional params
            foreach ($this->_specificTypes[$type] as $paramSuffix) {
                if (isset($rowData[self::COLUMN_PREFIX . $paramSuffix])) {
                    $data = $rowData[self::COLUMN_PREFIX . $paramSuffix];

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
            if (isset($rowData[self::COLUMN_PREFIX . $paramSuffix])) {
                $data = $rowData[self::COLUMN_PREFIX . $paramSuffix];

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
        if (!empty($rowData[self::COLUMN_ROW_TITLE]) && empty($rowData[self::COLUMN_STORE])) {
            $valueData = array(
                'option_type_id' => $optionTypeId,
                'sort_order'     => empty($rowData[self::COLUMN_ROW_SORT]) ? 0 : abs($rowData[self::COLUMN_ROW_SORT]),
                'sku'            => !empty($rowData[self::COLUMN_ROW_SKU]) ? $rowData[self::COLUMN_ROW_SKU] : ''
            );

            $priceData = false;
            if (!empty($rowData[self::COLUMN_ROW_PRICE])) {
                $priceData = array(
                    'price'      => (float) rtrim($rowData[self::COLUMN_ROW_PRICE], '%'),
                    'price_type' => 'fixed'
                );
                if ('%' == substr($rowData[self::COLUMN_ROW_PRICE], -1)) {
                    $priceData['price_type'] = 'percent';
                }
            }

            return array(
                'value' => $valueData,
                'title' => $rowData[self::COLUMN_ROW_TITLE],
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
        $this->_connection->delete($this->_tables['catalog_product_option'],
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
            $this->_connection->insertMultiple($this->_tables['catalog_product_option'], $options);
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
            $this->_connection->insertOnDuplicate($this->_tables['catalog_product_option_title'],
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
            $this->_connection->insertOnDuplicate($this->_tables['catalog_product_option_price'],
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
            $this->_connection->insertMultiple($this->_tables['catalog_product_option_type_value'],
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
            $this->_connection->insertOnDuplicate($this->_tables['catalog_product_option_type_price'],
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
            $this->_connection->insertOnDuplicate($this->_tables['catalog_product_option_type_title'],
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
            $this->_connection->insertOnDuplicate($this->_tables['catalog_product_entity'],
                $data,
                array('has_options', 'required_options', 'updated_at')
            );
        }

        return $this;
    }
}
