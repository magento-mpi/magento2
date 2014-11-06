<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Module;

use Magento\Framework\App\Resource\Config;

/**
 * Resource Model
 */
class Resource extends \Magento\Framework\Module\Resource
{
    const MAIN_TABLE = 'core_resource';

    /**
     * Class constructor
     *
     * @param \Magento\Framework\App\Resource $appResource
     * @internal param AdapterInterface $adapter
     * @internal param string $tablePrefix
     */
    public function __construct(\Magento\Framework\App\Resource $appResource)
    {
        parent::__construct($appResource);
        $connection = $appResource->getConnection(Config::DEFAULT_SETUP_CONNECTION);
        $this->_connections['write'] = $connection;
        $this->_connections['read'] = $connection;
    }
    /**
     * Get name of main table
     *
     * @return string
     */
    public function getMainTable() {
        return $this->_resources->getTableName(self::MAIN_TABLE);
    }
}
