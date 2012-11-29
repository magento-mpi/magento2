<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Controller_Varien_Router_Factory
{
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
     * @param string $className
     * @param array $routerInfo
     * @return mixed
     */
    public function createRouter($className, array $routerInfo = array())
    {
        $arguments = array(
            'areaCode'       => null,
            'baseController' => null,
        );
        if (isset($routerInfo['area'])) {
            $arguments['areaCode'] = $routerInfo['area'];
        }
        if (isset($routerInfo['base_controller'])) {
            $arguments['baseController'] = $routerInfo['base_controller'];
        }

        return $this->_objectManager->get($className, $arguments);
    }
}
