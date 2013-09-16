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
 * ImportExport customer_composite entity import data abstract resource model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Model_Resource_Import_CustomerComposite_Data
    extends Magento_ImportExport_Model_Resource_Import_Data
{
    /**
     * Entity type
     *
     * @var string
     */
    protected $_entityType = Magento_ImportExport_Model_Import_Entity_CustomerComposite::COMPONENT_ENTITY_CUSTOMER;

    /**
     * Customer attributes
     *
     * @var array
     */
    protected $_customerAttributes = array();

    /**
     * Class constructor
     *
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Helper_Data $coreHelper
     * @param array $arguments
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Core_Helper_Data $coreHelper,
        array $arguments = array()
    ) {
        parent::__construct($resource, $coreHelper, $arguments);

        if (isset($arguments['entity_type'])) {
            $this->_entityType = $arguments['entity_type'];
        }
        if (isset($arguments['customer_attributes'])) {
            $this->_customerAttributes = $arguments['customer_attributes'];
        }
    }

    /**
     * Get next bunch of validated rows.
     *
     * @return array|null
     */
    public function getNextBunch()
    {
        $bunchRows = parent::getNextBunch();
        if ($bunchRows != null) {
            $rows = array();
            foreach ($bunchRows as $rowNumber => $rowData) {
                $rowData = $this->_prepareRow($rowData);
                if ($rowData !== null) {
                    unset($rowData['_scope']);
                    $rows[$rowNumber] = $rowData;
                }
            }
            return $rows;
        } else {
            return $bunchRows;
        }
    }

    /**
     * Prepare row
     *
     * @param array $rowData
     * @return array
     */
    protected function _prepareRow(array $rowData)
    {
        $entityCustomer = Magento_ImportExport_Model_Import_Entity_CustomerComposite::COMPONENT_ENTITY_CUSTOMER;
        if ($this->_entityType == $entityCustomer) {
            if ($rowData['_scope'] == Magento_ImportExport_Model_Import_Entity_CustomerComposite::SCOPE_DEFAULT) {
                return $rowData;
            } else {
                return null;
            }
        } else {
            return $this->_prepareAddressRowData($rowData);
        }
    }

    /**
     * Prepare data row for address entity validation or import
     *
     * @param array $rowData
     * @return array
     */
    protected function _prepareAddressRowData(array $rowData)
    {
        $excludedAttributes = array(
            Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_DEFAULT_BILLING,
            Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_DEFAULT_SHIPPING
        );
        $prefix = Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_ADDRESS_PREFIX;

        $result = array();
        foreach ($rowData as $key => $value) {
            if (!in_array($key, $this->_customerAttributes)) {
                if (!in_array($key, $excludedAttributes)) {
                    $key = str_replace($prefix, '', $key);
                }
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
