<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Controller_Varien_Router_Default extends Magento_Core_Controller_Varien_Router_Abstract
{
    /**
     * @var Mage_Core_Model_NoRouteHandlerList
     */
    protected $_noRouteHandlerList;

    /**
     * @param Mage_Core_Controller_Varien_Action_Factory $controllerFactory
     * @param Mage_Core_Model_NoRouteHandlerList $noRouteHandlerList
     */
    public function __construct(
        Mage_Core_Controller_Varien_Action_Factory $controllerFactory,
        Mage_Core_Model_NoRouteHandlerList $noRouteHandlerList
    ) {
        parent::__construct($controllerFactory);
        $this->_noRouteHandlerList = $noRouteHandlerList;
    }

    /**
     * Modify request and set to no-route action
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @return boolean
     */
    public function match(Magento_Core_Controller_Request_Http $request)
    {
        foreach ($this->_noRouteHandlerList->getHandlers() as $noRouteHandler) {
            if ($noRouteHandler->process($request)) {
                break;
            }
        }

        return $this->_controllerFactory->createController('Magento_Core_Controller_Varien_Action_Forward',
            array('request' => $request)
        );
    }
}
