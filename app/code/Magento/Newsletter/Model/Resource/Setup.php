<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter resource setup
 */
class Magento_Newsletter_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
{
    /**
     * Get block factory
     *
     * @return Magento_Core_Model_Resource_Setup_Migration
     */
    public function getSetupMigration()
    {
        return $this->_migrationFactory->create(array('resourceName' => 'core_setup'));
    }
}
