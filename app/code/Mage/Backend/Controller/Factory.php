<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Controller factory
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Controller_Factory extends Mage_Core_Controller_Varien_Action_Factory
{
    /**
     * @param string $controllerName
     * @param array $arguments
     * @return mixed
     */
    public function createController($controllerName, array $arguments = array())
    {
        $context = $this->_objectManager->create('Mage_Backend_Controller_Context', $arguments);
        $arguments['context'] = $context;
        return $this->_objectManager->create($controllerName, $arguments);
    }
}
