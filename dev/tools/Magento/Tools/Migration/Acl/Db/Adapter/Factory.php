<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Db adapters factory
 */
class Magento_Tools_Migration_Acl_Db_Adapter_Factory
{
    /**
     * Get db adapter
     *
     * @param array $config
     * @param string $type
     * @throws InvalidArgumentException
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter(array $config, $type = null)
    {
        $dbAdapterClassName = 'Magento_Db_Adapter_Pdo_Mysql';

        if (false == empty($type)) {
            $dbAdapterClassName = $type;
        }

        if (false == class_exists($dbAdapterClassName, true)) {
            throw new InvalidArgumentException('Specified adapter not exists: ' . $dbAdapterClassName);
        }
        $adapter = new $dbAdapterClassName($config);

        if (false == ($adapter instanceof Zend_Db_Adapter_Abstract)) {
            unset($adapter);
            throw new InvalidArgumentException('Specified adapter is not instance of Zend_Db_Adapter_Abstract');
        }
        return $adapter;
    }
}
