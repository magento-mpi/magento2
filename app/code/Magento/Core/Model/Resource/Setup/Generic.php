<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Resource\Setup;

class Generic extends \Magento\Core\Model\Resource\Setup
{
    /**
     * Get migration instance
     *
     * @param array $data
     * @return \Magento\Core\Model\Resource\Setup\Migration
     */
    public function createMigrationSetup(array $data = array())
    {
        return $this->_migrationFactory->create($data);
    }
}
