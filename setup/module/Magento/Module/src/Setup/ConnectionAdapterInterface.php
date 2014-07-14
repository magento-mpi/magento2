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
     * @return \Magento\Db\Adapter\AdapterInterface|null
     */
    public function getConnection();
}
