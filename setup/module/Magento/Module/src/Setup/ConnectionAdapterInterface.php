<?php
/**
 * Connection adapter interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Setup;

interface ConnectionAdapterInterface
{
    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface|null
     */
    public function getConnection();
}
