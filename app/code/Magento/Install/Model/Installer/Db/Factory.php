<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Installer DB factory
 */
class Magento_Install_Model_Installer_Db_Factory
{
    protected $_types = array(
        'mysql4' => 'Magento_Install_Model_Installer_Db_Mysql4'
    );

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get Installer Db type instance
     *
     * @param string $type
     * @return Magento_Install_Model_Installer_Db_Abstract | bool
     * @throws InvalidArgumentException
     */
    public function get($type)
    {
        if (!empty($type) && isset($this->_types[(string)$type])) {
            return $this->_objectManager->get($this->_types[(string)$type]);
        }
        return false;
    }



}
