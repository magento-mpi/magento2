<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Resource\ImportExport\Import\CustomerComposite;

use Magento\Customer\Model\ImportExport\Import\CustomerComposite;

class Data extends \Magento\ImportExport\Model\Resource\Import\Data
{
    /**
     * Entity type
     *
     * @var string
     */
    protected $_entityType = CustomerComposite::COMPONENT_ENTITY_CUSTOMER;

    /**
     * Customer attributes
     *
     * @var array
     */
    protected $_customerAttributes = array();

    /**
     * Class constructor
     *
     * @param \Magento\App\Resource $resource
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param array $arguments
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Core\Helper\Data $coreHelper,
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
     * @return array|null
     */
    protected function _prepareRow(array $rowData)
    {
        $entityCustomer = CustomerComposite::COMPONENT_ENTITY_CUSTOMER;
        if ($this->_entityType == $entityCustomer) {
            if ($rowData['_scope'] == CustomerComposite::SCOPE_DEFAULT) {
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
            CustomerComposite::COLUMN_DEFAULT_BILLING,
            CustomerComposite::COLUMN_DEFAULT_SHIPPING
        );
        $prefix = CustomerComposite::COLUMN_ADDRESS_PREFIX;

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
