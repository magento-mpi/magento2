<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Controller_Varien_Router_Default extends Mage_Core_Controller_Varien_Router_Abstract
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
     * @param Mage_Core_Controller_Request_Http $request
     * @return boolean
     */
    public function match(Mage_Core_Controller_Request_Http $request)
    {
        foreach ($this->_noRouteHandlerList->getHandlers() as $noRouteHandler) {
            if ($noRouteHandler->process($request)) {
                break;
            }
        }

        return $this->_controllerFactory->createController('Mage_Core_Controller_Varien_Action_Forward',
            array('request' => $request)
        );
    }
}
