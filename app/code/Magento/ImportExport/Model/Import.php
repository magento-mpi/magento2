<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method string getBehavior() getBehavior()
 * @method Magento_ImportExport_Model_Import setEntity() setEntity(string $value)
 */
class Magento_ImportExport_Model_Import extends Magento_ImportExport_Model_Abstract
{
    /**
     * Import entities config key
     */
    const CONFIG_KEY_ENTITIES = 'global/importexport/import_entities';

    /**#@+
     * Import behaviors
     */
    const BEHAVIOR_APPEND     = 'append';
    const BEHAVIOR_ADD_UPDATE = 'add_update';
    const BEHAVIOR_REPLACE    = 'replace';
    const BEHAVIOR_DELETE     = 'delete';
    const BEHAVIOR_CUSTOM     = 'custom';
    /**#@-*/

    /**#@+
     * Form field names (and IDs)
     */
    const FIELD_NAME_SOURCE_FILE      = 'import_file';
    const FIELD_NAME_IMG_ARCHIVE_FILE = 'import_image_archive';
    /**#@-*/

    /**#@+
     * Import constants
     */
    const DEFAULT_SIZE      = 50;
    const MAX_IMPORT_CHUNKS = 4;
    /**#@-*/

    /**
     * Entity adapter.
     *
     * @var Magento_ImportExport_Model_Import_Entity_Abstract
     */
    protected $_entityAdapter;

    /**
     * Entity invalidated indexes.
     *
     * @var Magento_ImportExport_Model_Import_Entity_Abstract
     */
     protected static $_entityInvalidatedIndexes = array (
        'catalog_product' => array (
            'catalog_product_price',
            'catalog_category_product',
            'catalogsearch_fulltext',
            'catalog_product_flat',
        )
    );

    /**
     * Import export data
     *
     * @var Magento_ImportExport_Helper_Data
     */
    protected $_importExportData = null;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * @var Magento_ImportExport_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_ImportExport_Model_Import_Entity_Factory
     */
    protected $_entityFactory;

    /**
     * @var Magento_ImportExport_Model_Resource_Import_Data
     */
    protected $_importData;

    /**
     * @var Magento_ImportExport_Model_Export_Adapter_CsvFactory
     */
    protected $_csvFactory;

    /**
     * @var Zend_File_Transfer_Adapter_HttpFactory
     */
    protected $_httpFactory;

    /**
     * @var Magento_Core_Model_File_UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * @var Magento_Index_Model_Indexer
     */
    protected $_indexer;

    /**
     * @var Magento_ImportExport_Model_Source_Import_Behavior_Factory
     */
    protected $_behaviorFactory;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Dir $dir
     * @param Magento_Core_Model_Log_AdapterFactory $adapterFactory
     * @param Magento_ImportExport_Helper_Data $importExportData
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_ImportExport_Model_Config $config
     * @param Magento_ImportExport_Model_Import_Entity_Factory $entityFactory
     * @param Magento_ImportExport_Model_Resource_Import_Data $importData
     * @param Magento_ImportExport_Model_Export_Adapter_CsvFactory $csvFactory
     * @param Zend_File_Transfer_Adapter_HttpFactory $httpFactory
     * @param Magento_Core_Model_File_UploaderFactory $uploaderFactory
     * @param Magento_ImportExport_Model_Source_Import_Behavior_Factory $behaviorFactory
     * @param Magento_Index_Model_Indexer $indexer
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Dir $dir,
        Magento_Core_Model_Log_AdapterFactory $adapterFactory,
        Magento_ImportExport_Helper_Data $importExportData,
        Magento_Core_Model_Config $coreConfig,
        Magento_ImportExport_Model_Config $config,
        Magento_ImportExport_Model_Import_Entity_Factory $entityFactory,
        Magento_ImportExport_Model_Resource_Import_Data $importData,
        Magento_ImportExport_Model_Export_Adapter_CsvFactory $csvFactory,
        Zend_File_Transfer_Adapter_HttpFactory $httpFactory,
        Magento_Core_Model_File_UploaderFactory $uploaderFactory,
        Magento_ImportExport_Model_Source_Import_Behavior_Factory $behaviorFactory,
        Magento_Index_Model_Indexer $indexer,
        array $data = array()
    ) {
        $this->_importExportData = $importExportData;
        $this->_coreConfig = $coreConfig;
        $this->_config = $config;
        $this->_entityFactory = $entityFactory;
        $this->_importData = $importData;
        $this->_csvFactory = $csvFactory;
        $this->_httpFactory = $httpFactory;
        $this->_uploaderFactory = $uploaderFactory;
        $this->_indexer = $indexer;
        $this->_behaviorFactory = $behaviorFactory;
        parent::__construct($logger, $dir, $adapterFactory, $data);
    }

    /**
     * Create instance of entity adapter and return it
     *
     * @throws Magento_Core_Exception
     * @return Magento_ImportExport_Model_Import_Entity_Abstract|Magento_ImportExport_Model_Import_EntityAbstract
     */
    protected function _getEntityAdapter()
    {
        if (!$this->_entityAdapter) {
            $entityTypes = $this->_config->getModels(self::CONFIG_KEY_ENTITIES);

            if (isset($entityTypes[$this->getEntity()])) {
                try {
                    $this->_entityAdapter = $this->_entityFactory->create($entityTypes[$this->getEntity()]['model']);
                } catch (Exception $e) {
                    $this->_logger->logException($e);
                    throw new Magento_Core_Exception(
                        __('Please enter a correct entity model')
                    );
                }
                if (!($this->_entityAdapter instanceof Magento_ImportExport_Model_Import_Entity_Abstract)
                    && !($this->_entityAdapter instanceof Magento_ImportExport_Model_Import_EntityAbstract)
                ) {
                    throw new Magento_Core_Exception(
                        __('Entity adapter object must be an instance of %1 or %2',
                                'Magento_ImportExport_Model_Import_Entity_Abstract',
                                'Magento_ImportExport_Model_Import_EntityAbstract'));
                }

                // check for entity codes integrity
                if ($this->getEntity() != $this->_entityAdapter->getEntityTypeCode()) {
                    throw new Magento_Core_Exception(
                        __('The input entity code is not equal to entity adapter code.')
                    );
                }
            } else {
                throw new Magento_Core_Exception(__('Please enter a correct entity.'));
            }
            $this->_entityAdapter->setParameters($this->getData());
        }
        return $this->_entityAdapter;
    }

    /**
     * Returns source adapter object.
     *
     * @param string $sourceFile Full path to source file
     * @return Magento_ImportExport_Model_Import_SourceAbstract
     */
    protected function _getSourceAdapter($sourceFile)
    {
        return Magento_ImportExport_Model_Import_Adapter::findAdapterFor($sourceFile);
    }

    /**
     * Return operation result messages
     *
     * @param bool $validationResult
     * @return array
     */
    public function getOperationResultMessages($validationResult)
    {
        $messages = array();
        if ($this->getProcessedRowsCount()) {
            if (!$validationResult) {
                if ($this->getProcessedRowsCount() == $this->getInvalidRowsCount()) {
                    $messages[] = __('File is totally invalid. Please fix errors and re-upload file.');
                } elseif ($this->getErrorsCount() >= $this->getErrorsLimit()) {
                    $messages[] = __('Errors limit (%1) reached. Please fix errors and re-upload file.',
                            $this->getErrorsLimit());
                } else {
                    if ($this->isImportAllowed()) {
                        $messages[] = __('Please fix errors and re-upload file.');
                    } else {
                        $messages[] = __('File is partially valid, but import is not possible');
                    }
                }
                // errors info
                foreach ($this->getErrors() as $errorCode => $rows) {
                    $error = $errorCode . ' '
                        . __('in rows') . ': '
                        . implode(', ', $rows);
                    $messages[] = $error;
                }
            } else {
                if ($this->isImportAllowed()) {
                    $messages[] = __('Validation finished successfully');
                } else {
                    $messages[] = __('File is valid, but import is not possible');
                }
            }
            $notices = $this->getNotices();
            if (is_array($notices)) {
                $messages = array_merge($messages, $notices);
            }
            $messages[] = __('Checked rows: %1, checked entities: %2, invalid rows: %3, total errors: %4',
                    $this->getProcessedRowsCount(), $this->getProcessedEntitiesCount(),
                    $this->getInvalidRowsCount(), $this->getErrorsCount());
        } else {
            $messages[] = __('File does not contain data.');
        }
        return $messages;
    }

    /**
     * Get attribute type for upcoming validation.
     *
     * @param Magento_Eav_Model_Entity_Attribute_Abstract|Magento_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    public static function getAttributeType(Magento_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        if ($attribute->usesSource()) {
            return $attribute->getFrontendInput() == 'multiselect' ? 'multiselect' : 'select';
        } elseif ($attribute->isStatic()) {
            return $attribute->getFrontendInput() == 'date' ? 'datetime' : 'varchar';
        } else {
            return $attribute->getBackendType();
        }
    }

    /**
     * DB data source model getter.
     *
     * @return Magento_ImportExport_Model_Resource_Import_Data
     */
    public function getDataSourceModel()
    {
        return $this->_importData;
    }

    /**
     * Default import behavior getter.
     *
     * @static
     * @return string
     */
    public static function getDefaultBehavior()
    {
        return self::BEHAVIOR_APPEND;
    }

    /**
     * Override standard entity getter.
     *
     * @throws Magento_Core_Exception
     * @return string
     */
    public function getEntity()
    {
        if (empty($this->_data['entity'])) {
            throw new Magento_Core_Exception(__('Entity is unknown'));
        }
        return $this->_data['entity'];
    }

    /**
     * Get entity adapter errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_getEntityAdapter()->getErrorMessages();
    }

    /**
     * Returns error counter.
     *
     * @return int
     */
    public function getErrorsCount()
    {
        return $this->_getEntityAdapter()->getErrorsCount();
    }

    /**
     * Returns error limit value.
     *
     * @return int
     */
    public function getErrorsLimit()
    {
        return $this->_getEntityAdapter()->getErrorsLimit();
    }

    /**
     * Returns invalid rows count.
     *
     * @return int
     */
    public function getInvalidRowsCount()
    {
        return $this->_getEntityAdapter()->getInvalidRowsCount();
    }

    /**
     * Returns entity model noticees.
     *
     * @return array
     */
    public function getNotices()
    {
        return $this->_getEntityAdapter()->getNotices();
    }

    /**
     * Returns number of checked entities.
     *
     * @return int
     */
    public function getProcessedEntitiesCount()
    {
        return $this->_getEntityAdapter()->getProcessedEntitiesCount();
    }

    /**
     * Returns number of checked rows.
     *
     * @return int
     */
    public function getProcessedRowsCount()
    {
        return $this->_getEntityAdapter()->getProcessedRowsCount();
    }

    /**
     * Import/Export working directory (source files, result files, lock files etc.).
     *
     * @return string
     */
    public function getWorkingDir()
    {
        return $this->_dir->getDir('var') . DS . 'importexport' . DS;
    }

    /**
     * Import source file structure to DB.
     *
     * @return bool
     */
    public function importSource()
    {
        $this->setData(array(
            'entity'         => $this->getDataSourceModel()->getEntityTypeCode(),
            'behavior'       => $this->getDataSourceModel()->getBehavior(),
        ));

        $this->addLogComment(
            __('Begin import of "%1" with "%2" behavior',
                    $this->getEntity(),
                    $this->getBehavior()
                )
        );

        $result = $this->_getEntityAdapter()->importData();

        $this->addLogComment(array(
            __('Checked rows: %1, checked entities: %2, invalid rows: %3, total errors: %4',
                    $this->getProcessedRowsCount(),
                    $this->getProcessedEntitiesCount(),
                    $this->getInvalidRowsCount(),
                    $this->getErrorsCount()
                ),
            __('Import has been done successfuly.')
        ));

        return $result;
    }

    /**
     * Import possibility getter.
     *
     * @return bool
     */
    public function isImportAllowed()
    {
        return $this->_getEntityAdapter()->isImportAllowed();
    }

    /**
     * Import source file structure to DB.
     *
     * @return void
     */
    public function expandSource()
    {
        /** @var $writer Magento_ImportExport_Model_Export_Adapter_Csv */
        $writer  = $this->_csvFactory->create(array('destination' => $this->getWorkingDir() . "big0.csv"));
        $regExps = array('last' => '/(.*?)(\d+)$/', 'middle' => '/(.*?)(\d+)(.*)$/');
        $colReg  = array(
            'sku' => 'last', 'name' => 'last', 'description' => 'last', 'short_description' => 'last',
            'url_key' => 'middle', 'meta_title' => 'last', 'meta_keyword' => 'last', 'meta_description' => 'last',
            '_links_related_sku' => 'last', '_links_crosssell_sku' => 'last', '_links_upsell_sku' => 'last',
            '_custom_option_sku' => 'middle', '_custom_option_row_sku' => 'middle', '_super_products_sku' => 'last',
            '_associated_sku' => 'last'
        );
        $size = self::DEFAULT_SIZE;

        $filename = 'catalog_product.csv';
        $filenameFormat = 'big%s.csv';
        foreach ($this->_getSourceAdapter($this->getWorkingDir() . $filename) as $row) {
            $writer->writeRow($row);
        }
        $count = self::MAX_IMPORT_CHUNKS;
        for ($i = 1; $i < $count; $i++) {
            $writer = $this->_csvFactory->create(
                array('destination' => $this->getWorkingDir() . sprintf($filenameFormat, $i))
            );

            $adapter = $this->_getSourceAdapter($this->getWorkingDir() . sprintf($filenameFormat, $i - 1));
            foreach ($adapter as $row) {
                $writer->writeRow($row);
            }
            $adapter = $this->_getSourceAdapter($this->getWorkingDir() . sprintf($filenameFormat, $i - 1));
            foreach ($adapter as $row) {
                foreach ($colReg as $colName => $regExpType) {
                    if (!empty($row[$colName])) {
                        preg_match($regExps[$regExpType], $row[$colName], $matches);

                        $row[$colName] = $matches[1] . ($matches[2] + $size)
                            . ('middle' == $regExpType ? $matches[3] : '');
                    }
                }
                $writer->writeRow($row);
            }
            $size *= 2;
        }
    }

    /**
     * Move uploaded file and create source adapter instance.
     *
     * @throws Magento_Core_Exception
     * @return string Source file path
     */
    public function uploadSource()
    {
        /** @var $adapter Zend_File_Transfer_Adapter_Http */
        $adapter  = $this->_httpFactory->create();
        if (!$adapter->isValid(self::FIELD_NAME_SOURCE_FILE)) {
            $errors = $adapter->getErrors();
            if ($errors[0] == Zend_Validate_File_Upload::INI_SIZE) {
                $errorMessage = $this->_importExportData->getMaxUploadSizeMessage();
            } else {
                $errorMessage = __('File was not uploaded.');
            }
            throw new Magento_Core_Exception($errorMessage);
        }

        $entity    = $this->getEntity();
        /** @var $uploader Magento_Core_Model_File_Uploader */
        $uploader  = $this->_uploaderFactory->create(array('fileId' => self::FIELD_NAME_SOURCE_FILE));
        $uploader->skipDbProcessing(true);
        $result    = $uploader->save($this->getWorkingDir());
        $extension = pathinfo($result['file'], PATHINFO_EXTENSION);

        $uploadedFile = $result['path'] . $result['file'];
        if (!$extension) {
            unlink($uploadedFile);
            throw new Magento_Core_Exception(__('Uploaded file has no extension'));
        }
        $sourceFile = $this->getWorkingDir() . $entity;

        $sourceFile .= '.' . $extension;

        if (strtolower($uploadedFile) != strtolower($sourceFile)) {
            if (file_exists($sourceFile)) {
                unlink($sourceFile);
            }

            if (!@rename($uploadedFile, $sourceFile)) {
                throw new Magento_Core_Exception(__('Source file moving failed'));
            }
        }
        $this->_removeBom($sourceFile);
        // trying to create source adapter for file and catch possible exception to be convinced in its adequacy
        try {
            $this->_getSourceAdapter($sourceFile);
        } catch (Exception $e) {
            unlink($sourceFile);
            throw new Magento_Core_Exception($e->getMessage());
        }
        return $sourceFile;
    }

    /**
     * Remove BOM from a file
     *
     * @param string $sourceFile
     * @return $this
     */
    protected function _removeBom($sourceFile)
    {
        $string = file_get_contents($sourceFile);
        if ($string !== false && substr($string, 0, 3) == pack("CCC", 0xef, 0xbb, 0xbf)) {
            $string = substr($string, 3);
            file_put_contents($sourceFile, $string);
        }
        return $this;
    }

    /**
     * Validates source file and returns validation result.
     *
     * @param Magento_ImportExport_Model_Import_SourceAbstract $source
     * @return bool
     */
    public function validateSource(Magento_ImportExport_Model_Import_SourceAbstract $source)
    {
        $this->addLogComment(__('Begin data validation'));
        $adapter = $this->_getEntityAdapter()->setSource($source);
        $result = $adapter->isDataValid();

        $messages = $this->getOperationResultMessages($result);
        $this->addLogComment($messages);
        if ($result) {
            $this->addLogComment(__('Done import data validation'));
        }
        return $result;
    }

    /**
     * Invalidate indexes by process codes.
     *
     * @return Magento_ImportExport_Model_Import
     */
    public function invalidateIndex()
    {
        if (!isset(self::$_entityInvalidatedIndexes[$this->getEntity()])) {
            return $this;
        }

        $indexers = self::$_entityInvalidatedIndexes[$this->getEntity()];
        foreach ($indexers as $indexer) {
            $indexProcess = $this->_indexer->getProcessByCode($indexer);
            if ($indexProcess) {
                $indexProcess->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
            }
        }

        return $this;
    }

    /**
     * Gets array of customer entities and appropriate behaviours
     * array(
     *     <entity_code> => array(
     *         'token' => <behavior_class_name>,
     *         'code'  => <behavior_model_code>,
     *     ),
     *     ...
     * )
     *
     * @static
     * @return array
     */
    public function getEntityBehaviors()
    {
        $behaviourData = array();
        $entitiesConfig = $this->_coreConfig->getNode(self::CONFIG_KEY_ENTITIES)->asArray();
        foreach ($entitiesConfig as $entityCode => $entityData) {
            $behaviorToken = isset($entityData['behavior_token']) ? $entityData['behavior_token'] : null;
            if ($behaviorToken && class_exists($behaviorToken)) {
                /** @var $behaviorModel Magento_ImportExport_Model_Source_Import_BehaviorAbstract */
                $behaviorModel = $this->_behaviorFactory->create($behaviorToken);
                $behaviourData[$entityCode] = array(
                    'token' => $behaviorToken,
                    'code'  => $behaviorModel->getCode() . '_behavior',
                );
            } else {
                throw new Magento_Core_Exception(__('Invalid behavior token for %1', $entityCode));
            }
        }
        return $behaviourData;
    }

    /**
     * Get array of unique entity behaviors
     * array(
     *     <behavior_model_code> => <behavior_class_name>,
     *     ...
     * )
     *
     * @static
     * @return array
     */
    public function getUniqueEntityBehaviors()
    {
        $uniqueBehaviors = array();
        $behaviourData = $this->getEntityBehaviors();
        foreach ($behaviourData as $behavior) {
            $behaviorCode = $behavior['code'];
            if (!isset($uniqueBehaviors[$behaviorCode])) {
                $uniqueBehaviors[$behaviorCode] = $behavior['token'];
            }
        }
        return $uniqueBehaviors;
    }
}
