<?php
/**
 * Test framework custom connection adapter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\Db;

class ConnectionAdapter extends \Magento\Core\Model\Resource\Type\Db\Pdo\Mysql
{
    /**
     * Retrieve DB adapter class name
     *
     * @return string
     */
    protected function _getDbAdapterClassName()
    {
        return 'Magento\TestFramework\Db\Adapter\Mysql';
    }
}
