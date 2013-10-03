<?php
/**
 * Connection adapter interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource;

interface ConnectionAdapterInterface
{
    /**
     * Get connection
     *
     * @return \Magento\DB\Adapter\AdapterInterface|null
     */
    public function getConnection();
}
