<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order archive model
 *
 */
class Enterprise_SalesArchive_Model_Order_Status_Source extends Mage_Backend_Model_Config_Source_Order_Status
{
    /**
     * Retrieve order statuses as options for select
     *
     * @see Mage_Backend_Model_Config_Source_Order_Status:toOptionArray()
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        array_shift($options); // Remove '--please select--' option
        return $options;
    }
}
