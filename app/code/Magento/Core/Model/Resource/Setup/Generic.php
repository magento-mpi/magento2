<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Resource_Setup_Generic extends Magento_Core_Model_Resource_Setup
{
    /**
     * Get migration instance
     *
     * @param array $data
     * @return Magento_Core_Model_Resource_Setup_Migration
     */
    public function createMigrationSetup(array $data = array())
    {
        return $this->_migrationFactory->create($data);
    }
}
