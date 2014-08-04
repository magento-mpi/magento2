<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Setup\Connection;

interface AdapterInterface
{
    /**
     * Get connection
     *
     * @param array $config
     * @return \Magento\Framework\DB\Adapter\AdapterInterface|null
     */
    public function getConnection(array $config = array());
}
