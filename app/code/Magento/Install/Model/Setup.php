<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Model;

class Setup extends \Magento\Framework\Module\Setup
{
    /**
     * Save configuration data
     *
     * @param string $path
     * @param string $value
     * @param int|string $scope
     * @param int $scopeId
     * @return $this
     */
    public function setConfigData($path, $value, $scope = \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT, $scopeId = 0)
    {
        $table = $this->getTable('core_config_data');
        // this is a fix for mysql 4.1
        $this->getConnection()->showTableStatus($table);

        $data = array('scope' => $scope, 'scope_id' => $scopeId, 'path' => $path, 'value' => $value);
        $this->getConnection()->insertOnDuplicate($table, $data, array('value'));
        return $this;
    }
}
