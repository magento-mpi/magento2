<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export customer finance entity model
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Model_Export_Entity_V2_Finance extends Mage_ImportExport_Model_Export_Entity_V2_Abstract
{
    /**
     * Export process.
     *
     * @return string
     */
    public function export()
    {
        // TODO: Implement export() method.
    }

    /**
     * Entity type code getter
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'customer_finance';
    }

    /**
     * Entity attributes collection getter
     *
     * @return Varien_Data_Collection
     */
    public function getAttributeCollection()
    {
        return new Varien_Data_Collection();
    }
}
