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
 * Export model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Export extends Mage_ImportExport_Model_Abstract
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
     * @var Mage_ImportExport_Model_Export_Entity_Abstract
     */
    protected $_entityAdapter;

    /**
     * Writer object instance.
     *
     * @var Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    protected $_writer;

    /**
     * Create instance of entity adapter and return it
     *
     * @throws Mage_Core_Exception
     * @return Mage_ImportExport_Model_Export_Entity_Abstract|Mage_ImportExport_Model_Export_EntityAbstract
     */
    protected function _getEntityAdapter()
    {
        if (!$this->_entityAdapter) {
            $entityTypes = Mage_ImportExport_Model_Config::getModels(self::CONFIG_KEY_ENTITIES);

            if (isset($entityTypes[$this->getEntity()])) {
                try {
                    $this->_entityAdapter = Mage::getModel($entityTypes[$this->getEntity()]['model']);
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::throwException(
                        Mage::helper('Mage_ImportExport_Helper_Data')->__('Please enter a correct entity model')
                    );
                }
                if (!($this->_entityAdapter instanceof Mage_ImportExport_Model_Export_Entity_Abstract)
                    && !($this->_entityAdapter instanceof Mage_ImportExport_Model_Export_EntityAbstract)
                ) {
                    Mage::throwException(
                        Mage::helper('Mage_ImportExport_Helper_Data')
                            ->__('Entity adapter object must be an instance of %s or %s',
                                'Mage_ImportExport_Model_Export_Entity_Abstract',
                                'Mage_ImportExport_Model_Export_EntityAbstract'
                            )
                    );
                }

                // check for entity codes integrity
                if ($this->getEntity() != $this->_entityAdapter->getEntityTypeCode()) {
                    Mage::throwException(
                        Mage::helper('Mage_ImportExport_Helper_Data')
                            ->__('The input entity code is not equal to entity adapter code.')
                    );
                }
            } else {
                Mage::throwException(Mage::helper('Mage_ImportExport_Helper_Data')->__('Please enter a correct entity.'));
            }
            $this->_entityAdapter->setParameters($this->getData());
        }
        return $this->_entityAdapter;
    }

    /**
     * Get writer object.
     *
     * @throws Mage_Core_Exception
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    protected function _getWriter()
    {
        if (!$this->_writer) {
            $validWriters = Mage_ImportExport_Model_Config::getModels(self::CONFIG_KEY_FORMATS);

            if (isset($validWriters[$this->getFileFormat()])) {
                try {
                    $this->_writer = Mage::getModel($validWriters[$this->getFileFormat()]['model']);
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::throwException(
                        Mage::helper('Mage_ImportExport_Helper_Data')->__('Please enter a correct entity model')
                    );
                }
                if (! $this->_writer instanceof Mage_ImportExport_Model_Export_Adapter_Abstract) {
                    Mage::throwException(
                        Mage::helper('Mage_ImportExport_Helper_Data')
                            ->__('Adapter object must be an instance of %s',
                                'Mage_ImportExport_Model_Export_Adapter_Abstract'
                            )
                    );
                }
            } else {
                Mage::throwException(Mage::helper('Mage_ImportExport_Helper_Data')->__('Please correct the file format.'));
            }
        }
        return $this->_writer;
    }

    /**
     * Export data.
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    public function export()
    {
        if (isset($this->_data[self::FILTER_ELEMENT_GROUP])) {
            $this->addLogComment(Mage::helper('Mage_ImportExport_Helper_Data')
                ->__('Begin export of %s', $this->getEntity()));
            $result = $this->_getEntityAdapter()
                ->setWriter($this->_getWriter())
                ->export();
            $countRows = substr_count(trim($result), "\n");
            if (!$countRows) {
                Mage::throwException(
                    Mage::helper('Mage_ImportExport_Helper_Data')->__('There is no data for export')
                );
            }
            if ($result) {
                $this->addLogComment(array(
                    Mage::helper('Mage_ImportExport_Helper_Data')->__('Exported %s rows.', $countRows),
                    Mage::helper('Mage_ImportExport_Helper_Data')->__('Export has been done.')
                ));
            }
            return $result;
        } else {
            Mage::throwException(
                Mage::helper('Mage_ImportExport_Helper_Data')->__('Please provide filter data.')
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
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @throws Exception
     * @return string
     */
    public static function getAttributeFilterType(Mage_Eav_Model_Entity_Attribute $attribute)
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
            Mage::throwException(
                Mage::helper('Mage_ImportExport_Helper_Data')->__('Cannot determine attribute filter type')
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
            Mage::throwException(Mage::helper('Mage_ImportExport_Helper_Data')->__('Entity is unknown'));
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
            Mage::throwException(Mage::helper('Mage_ImportExport_Helper_Data')->__('File format is unknown'));
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
        if ($entityAdapter instanceof Mage_ImportExport_Model_Export_EntityAbstract) {
            $fileName = $entityAdapter->getFileName();
        }
        if (!$fileName) {
            $fileName = $this->getEntity();
        }
        return $fileName . '_' . date('Ymd_His') .  '.' . $this->_getWriter()->getFileExtension();
    }
}
