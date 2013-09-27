<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource helper pool
 */
class Magento_Core_Model_Resource_HelperPool
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;


    /**
     * @var array
     */
    protected $_resourceHelpers = array();

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Magento_ObjectManager $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get resource helper singleton
     *
     * @param string $moduleName
     * @throws InvalidArgumentException
     * @return Magento_Core_Model_Resource_Helper_Abstract
     */
    public function get($moduleName)
    {
        $helperClassName = $moduleName . '_Model_Resource_Helper';
        $connection = strtolower($moduleName);
        if (substr($moduleName, 0, 8) == 'Magento_') {
            $connection = substr($connection, 8);
        }

        if (!isset($this->_resourceHelpers[$connection])) {
            $helper = $this->_objectManager->create($helperClassName, array('modulePrefix' => $connection));
            if (false === ($helper instanceof Magento_Core_Model_Resource_Helper_Abstract)) {
                throw new InvalidArgumentException(
                    $helperClassName . ' doesn\'t extend Magento_Core_Model_Resource_Helper_Abstract'
                );
            }
            $this->_resourceHelpers[$connection] = $helper;
        }

        return $this->_resourceHelpers[$connection];
    }
}
