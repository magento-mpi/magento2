<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for tax classes
 */
interface Magento_Tax_Model_TaxClass_Type_Interface
{
    /**
     * Get Collection of Objects that are assigned to this tax class
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getAssignedToObjects();

    /**
     * Get Collection of Tax Rules that are assigned to this tax class
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getAssignedToRules();

    /**
     * Get Name of Objects that use this Tax Class Type
     *
     * @return string
     */
    public function getObjectTypeName();
}
