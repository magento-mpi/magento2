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
 * Export model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Model_Export extends Magento_ImportExport_Model_Abstract
{
    const FILTER_ELEMENT_GROUP = 'export_filter';
    const FILTER_ELEMENT_SKIP  = 'skip_attr';

    /**
     * Filter fields types.
     */
    const FILTER_TYPE_SELECT = 'select';
    const FILTER_TYPE_INPUT  = 'input';
    const FILTER_TYPE_DATE   = 'date';
    const FILTER_TYPE_NUMBER = 'number';

    /**#@+
     * Config keys.
     */
    const CONFIG_KEY_ENTITIES          = 'global/importexport/export_entities';
    const CONFIG_KEY_FORMATS           = 'global/importexport/export_file_formats';
    /**#@-*/

    /**
     * Entity adapter.
     *
     * @var Magento_ImportExport_Model_Export_Entity_Abstract
     */
    protected $_entityAdapter;

    /**
     * Writer object instance.
     *
     * @var Magento_ImportExport_Model_Export_Adapter_Abstract
     */
    protected $_writer;

    /**
     * @var Magento_ImportExport_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_ImportExport_Model_Export_Entity_Factory
     */
    protected $_entityFactory;

    /**
     * @var Magento_ImportExport_Model_Export_Adapter_Factory
     */
    protected $_exportAdapterFac;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Dir $dir
     * @param Magento_Core_Model_Log_AdapterFactory $adapterFactory
     * @param Magento_ImportExport_Model_Config $config
     * @param Magento_ImportExport_Model_Export_Entity_Factory $entityFactory
     * @param Magento_ImportExport_Model_Export_Adapter_Factory $exportAdapterFac
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Dir $dir,
        Magento_Core_Model_Log_AdapterFactory $adapterFactory,
        Magento_ImportExport_Model_Config $config,
        Magento_ImportExport_Model_Export_Entity_Factory $entityFactory,
        Magento_ImportExport_Model_Export_Adapter_Factory $exportAdapterFac,
        array $data = array()
    ) {
        $this->_config = $config;
        $this->_entityFactory = $entityFactory;
        $this->_exportAdapterFac = $exportAdapterFac;
        parent::__construct($logger, $dir, $adapterFactory, $data);
    }

    /**
     * Create instance of entity adapter and return it
     *
     * @throws Magento_Core_Exception
     * @return Magento_ImportExport_Model_Export_Entity_Abstract|Magento_ImportExport_Model_Export_EntityAbstract
     */
    protected function _getEntityAdapter()
    {
        if (!$this->_entityAdapter) {
            $entityTypes = $this->_config->getModels(self::CONFIG_KEY_ENTITIES);

            if (isset($entityTypes[$this->getEntity()])) {
                try {
                    $this->_entityAdapter = $this->_exportAdapterFac->create(
                        array('fileName' => $entityTypes[$this->getEntity()]['model'])
                    );
                } catch (Exception $e) {
                    $this->_logger->logException($e);
                    throw new Magento_Core_Exception(
                        __('Please enter a correct entity model')
                    );
                }
                if (!($this->_entityAdapter instanceof Magento_ImportExport_Model_Export_Entity_Abstract)
                    && !($this->_entityAdapter instanceof Magento_ImportExport_Model_Export_EntityAbstract)
                ) {
                    throw new Magento_Core_Exception(
                        __('Entity adapter object must be an instance of %1 or %2',
                                'Magento_ImportExport_Model_Export_Entity_Abstract',
                                'Magento_ImportExport_Model_Export_EntityAbstract'
                            )
                    );
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
     * Get writer object.
     *
     * @throws Magento_Core_Exception
     * @return Magento_ImportExport_Model_Export_Adapter_Abstract
     */
    protected function _getWriter()
    {
        if (!$this->_writer) {
            $validWriters = $this->_config->getModels(self::CONFIG_KEY_FORMATS);

            if (isset($validWriters[$this->getFileFormat()])) {
                try {
                    $this->_writer = $this->_exportAdapterFac->create(
                        array('fileName' => $validWriters[$this->getFileFormat()]['model'])
                    );
                } catch (Exception $e) {
                    $this->_logger->logException($e);
                    throw new Magento_Core_Exception(
                        __('Please enter a correct entity model')
                    );
                }
                if (! $this->_writer instanceof Magento_ImportExport_Model_Export_Adapter_Abstract) {
                    throw new Magento_Core_Exception(
                        __('Adapter object must be an instance of %1',
                                'Magento_ImportExport_Model_Export_Adapter_Abstract'
                            )
                    );
                }
            } else {
                throw new Magento_Core_Exception(__('Please correct the file format.'));
            }
        }
        return $this->_writer;
    }

    /**
     * Export data.
     *
     * @throws Magento_Core_Exception
     * @return string
     */
    public function export()
    {
        if (isset($this->_data[self::FILTER_ELEMENT_GROUP])) {
            $this->addLogComment(__('Begin export of %1', $this->getEntity()));
            $result = $this->_getEntityAdapter()
                ->setWriter($this->_getWriter())
                ->export();
            $countRows = substr_count(trim($result), "\n");
            if (!$countRows) {
                throw new Magento_Core_Exception(
                    __('There is no data for export')
                );
            }
            if ($result) {
                $this->addLogComment(array(
                    __('Exported %1 rows.', $countRows),
                    __('Export has been done.')
                ));
            }
            return $result;
        } else {
            throw new Magento_Core_Exception(
                __('Please provide filter data.')
            );
        }
    }

    /**
     * Clean up already loaded attribute collection.
     *
     * @param Magento_Data_Collection $collection
     * @return Magento_Data_Collection
     */
    public function filterAttributeCollection(Magento_Data_Collection $collection)
    {
        return $this->_getEntityAdapter()->filterAttributeCollection($collection);
    }

    /**
     * Determine filter type for specified attribute.
     *
     * @static
     * @param Magento_Eav_Model_Entity_Attribute $attribute
     * @throws Exception
     * @return string
     */
    public static function getAttributeFilterType(Magento_Eav_Model_Entity_Attribute $attribute)
    {
        if ($attribute->usesSource() || $attribute->getFilterOptions()) {
            return self::FILTER_TYPE_SELECT;
        } elseif ('datetime' == $attribute->getBackendType()) {
            return self::FILTER_TYPE_DATE;
        } elseif ('decimal' == $attribute->getBackendType() || 'int' == $attribute->getBackendType()) {
            return self::FILTER_TYPE_NUMBER;
        } elseif ($attribute->isStatic()
                  || 'varchar' == $attribute->getBackendType()
                  || 'text' == $attribute->getBackendType()
        ) {
            return self::FILTER_TYPE_INPUT;
        } else {
            throw new Magento_Core_Exception(
                __('Cannot determine attribute filter type')
            );
        }
    }

    /**
     * MIME-type for 'Content-Type' header.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->_getWriter()->getContentType();
    }

    /**
     * Override standard entity getter.
     *
     * @throw Exception
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
     * Entity attributes collection getter.
     *
     * @return Magento_Data_Collection
     */
    public function getEntityAttributeCollection()
    {
        return $this->_getEntityAdapter()->getAttributeCollection();
    }

    /**
     * Override standard entity getter.
     *
     * @throw Exception
     * @return string
     */
    public function getFileFormat()
    {
        if (empty($this->_data['file_format'])) {
            throw new Magento_Core_Exception(__('File format is unknown'));
        }
        return $this->_data['file_format'];
    }

    /**
     * Return file name for downloading.
     *
     * @return string
     */
    public function getFileName()
    {
        $fileName = null;
        $entityAdapter = $this->_getEntityAdapter();
        if ($entityAdapter instanceof Magento_ImportExport_Model_Export_EntityAbstract) {
            $fileName = $entityAdapter->getFileName();
        }
        if (!$fileName) {
            $fileName = $this->getEntity();
        }
        return $fileName . '_' . date('Ymd_His') .  '.' . $this->_getWriter()->getFileExtension();
    }
}
