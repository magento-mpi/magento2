<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Oracle resource data model
 *
 * @category   Mage
 * @package    Mage_Install
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Installer_Db_Oracle extends Mage_Install_Model_Installer_Db_Abstract
{
    /**
     * Retrieve DB server version
     *
     * @return string (string version number | 'undefined')
     */
    public function getVersion()
    {
        $adapter    = $this->_getConnection();
        $select     = $adapter->select()
            ->from('product_component_version', 'version')
            ->where('product LIKE ?', 'Oracle%');
        $version    = $adapter->fetchOne($select);
        return $version ? $version : 'undefined';
    }
}
