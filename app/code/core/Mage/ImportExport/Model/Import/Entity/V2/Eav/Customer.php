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
 * Import entity customer model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer
    extends Mage_ImportExport_Model_Import_Entity_V2_Eav_Abstract
{
    /**
     * Import data rows
     *
     * @abstract
     * @return boolean
     */
    protected function _importData()
    {
        // TODO: need to implement
        return false;
    }

    /**
     * EAV entity type code getter
     *
     * @abstract
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'customer';
    }

    // @codingStandardsIgnoreStart

    /**
     * Validate data row
     *
     * @param array $rowData
     * @param int $rowNum
     * @return boolean
     */
    public function validateRow(array $rowData, $rowNum)
    {
        // TODO: need to implement
        return false;
    }

    // @codingStandardsIgnoreEnd
}
