<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reminder resource setup
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reminder\Model\Resource;

class Setup extends \Magento\Core\Model\Resource\Setup
{
    /**
     * @param array $data
     * @return \Magento\Core\Model\Resource\Setup\Migration
     */
    public function createMigrationFactory($data = array())
    {
        return $this->_migrationFactory->create($data);
    }
}
