<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Db;

class ConnectionFactory extends \Magento\Framework\App\Resource\ConnectionFactory
{
    /**
     * {@inheritdoc}
     */
    public function create(array $connectionConfig)
    {
        $connectionConfig['adapter'] = 'Magento\TestFramework\Db\ConnectionAdapter';
        return parent::create($connectionConfig);
    }
}
