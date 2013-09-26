<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource setup model
 */
class Magento_SalesRule_Model_Resource_Setup extends Magento_Sales_Model_Resource_Setup
{
    /**
     * @return Magento_Core_Model_Resource_Setup_Migration
     */
    public function getMigrationModel()
    {
        return $this->_migrationFactory->create(array(
            'resourceName' => 'core_setup'
        ));
    }
}

