<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Controller factory
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Controller_Factory extends Magento_Core_Controller_Varien_Action_Factory
{
    /**
     * @param string $controllerName
     * @param array $arguments
     * @return mixed
     */
    public function createController($controllerName, array $arguments = array())
    {
        $context = $this->_objectManager->create('Magento_Backend_Controller_Context', $arguments);
        $arguments['context'] = $context;
        return $this->_objectManager->create($controllerName, $arguments);
    }
}
