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
namespace Magento\Backend\Controller;

class Factory extends \Magento\Core\Controller\Varien\Action\Factory
{
    /**
     * @param string $controllerName
     * @param array $arguments
     * @return mixed
     */
    public function createController($controllerName, array $arguments = array())
    {
        $context = $this->_objectManager->create('Magento\Backend\Controller\Context', $arguments);
        $arguments['context'] = $context;
        return $this->_objectManager->create($controllerName, $arguments);
    }
}
