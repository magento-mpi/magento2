<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Module\Setup\Connection;

interface AdapterInterface
{
    /**
     * Get connection
     *
     * @param array $config
     * @return \Magento\Setup\Framework\DB\Adapter\AdapterInterface|null
     */
    public function getConnection(array $config = array());
}
