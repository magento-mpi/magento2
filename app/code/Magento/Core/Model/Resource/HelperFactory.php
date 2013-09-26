<?php
/**
 * Resource helper factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Magento_Core_Model_Resource_HelperFactory
{
    /**
     * @var string
     */
    protected $_moduleName;

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
     * Create resource helper instance
     *
     * @param string $moduleName
     * @return Magento_Core_Model_Resource_Helper_Abstract
     * @throws LogicException
     * @throws InvalidArgumentException
     */
    protected function _create($moduleName)
    {
        if (empty($moduleName)) {
            throw new InvalidArgumentException(__('Undefined module name'));
        }
        $connectionModel = $this->_objectManager
            ->get('Magento_Core_Model_Config_Resource')
            ->getResourceConnectionModel('core');

        $helperClassName = $moduleName . '_Model_Resource_Helper_' . ucfirst($connectionModel);
        $connection = strtolower($moduleName);
        if (substr($moduleName, 0, 8) == 'Magento_') {
            $connection = substr($connection, 8);
        }
        $object = $this->_objectManager->create($helperClassName, array('modulePrefix' => $connection));

        if (false == ($object instanceof Magento_Core_Model_Resource_Helper_Abstract)) {
            throw new LogicException(
                $helperClassName . ' doesn\'t implement Magento_Core_Model_Resource_Helper_Abstract'
            );
        }
        return $object;
    }

    /**
     * Create resource helper instance
     *
     */
    public function create()
    {
        return $this->_create($this->_moduleName);
    }
}
