<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * adminhtml_controller_action_predispatch_start event observer
 * Class Saas_Backend_Model_Cache_Observer
 */
class Saas_Backend_Model_Cache_Observer
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function __construct(Mage_Core_Controller_Request_Http $request)
    {
        $this->_request = $request;
    }

    /**
     * Redirects to noRoute from actions of admin cache controller
     *
     * @param Varien_Event_Observer $observer
     */
    public function controllerActionFilter(Varien_Event_Observer $observer)
    {
        $forbiddenActionList = array(
            'index',
            'flushAll',
            'flushSystem',
            'massEnable',
            'massDisable',
            'massRefresh',
            'cleanMedia',
            'cleanImages',
        );

        if ($this->_request->getControllerName() == "cache"
            && in_array($this->_request->getActionName(), $forbiddenActionList)) {
            $this->_request->setRouteName('noRoute');
        }
    }
}
