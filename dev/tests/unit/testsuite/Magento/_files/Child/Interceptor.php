<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_Test_Di_Child_Interceptor extends Magento_Test_Di_Child
{
    /**
     * @var Magento_ObjectManager_Config
     */
    protected $_config;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_plugins = array();

    /**
     * @var array
     */
    protected $_pluginList = array();

    public function __construct(Magento_ObjectManager $objectManager, $pluginList)
    {
        $this->_objectManager = $objectManager;
        $this->_pluginList = $pluginList;
    }

    protected function _getSubject()
    {
        return $this->_objectManager->get('Magento_Test_Di_Child');
    }

    public function wrap($param)
    {
        foreach ($this->_pluginList as $key => $plugin) {
            if (!isset($this->_plugins[$key])) {
                $this->_plugins[$key] = $this->_objectManager->get($plugin['instance']);
            }
            if (is_callable(array($this->_plugins[$key], 'wrapBefore'))) {
                $param = $this->_plugins[$key]->wrapBefore($param);
            }
        }
        $returnValue = $this->_getSubject()->wrap($param);
        foreach (array_reverse($this->_plugins) as $key => $plugin) {
            if (is_callable(array($this->_plugins[$key], 'wrapAfter'))) {
                $returnValue = $this->_plugins[$key]->wrapAfter($returnValue);
            }
        }
        return $returnValue;
    }
}
