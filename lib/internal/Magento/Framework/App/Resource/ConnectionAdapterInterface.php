<?php
/**
 * Connection adapter interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Resource;

use Magento\Framework\DB\LoggerInterface;

interface ConnectionAdapterInterface
{
    /**
     * Get connection
     *
     * @param LoggerInterface $logger
     * @return \Magento\Framework\DB\Adapter\AdapterInterface|null
     */
    public function getConnection(LoggerInterface $logger);
}
