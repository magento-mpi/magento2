<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model\Resource;

/**
 * Resource setup model
 */
class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * @return \Magento\Module\Setup\Migration
     */
    public function getMigrationModel()
    {
        return $this->_migrationFactory->create(array('resourceName' => 'core_setup'));
    }
}
