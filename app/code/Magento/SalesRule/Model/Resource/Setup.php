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
namespace Magento\SalesRule\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * @return \Magento\Core\Model\Resource\Setup\Migration
     */
    public function getMigrationModel()
    {
        return $this->_migrationFactory->create(array(
            'resourceName' => 'core_setup'
        ));
    }
}

