<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

abstract class Enterprise_Staging_Model_Staging_State_Abstract extends Varien_Object
{
    /**
     * Staging instance
     *
     * @var mixed
     */
    protected $_staging;

    /**
     * Adapter instance
     *
     * @var mixed
     */
    protected $_adapter;

    /**
     * Scenario instance
     *
     * @var mixed
     */
    protected $_scenario;

    /**
     * Website instance
     *
     * @var mixed
     */
    protected $_website;

    /**
     * State Xml config
     *
     * @var Varien_Simplexml_Element
     */
    protected $_config;
        
    /**
     * Store Group instance
     *
     * @var mixed
     */
    protected $_group;

    /**
     * Store instance
     *
     * @var mixed
     */
    protected $_store;

    protected $_addToEventHistory = false;

    protected $_eventStateCode;

    protected $_eventStateLabel;
    
    protected $_eventStateStatuses;

    public function __construct()
    {
        $this->_read  = Mage::getSingleton('core/resource')->getConnection('staging_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('staging_write');
    }

    final public function run($staging = null)
    {
        if (is_null($staging)) {
            $staging = $this->getStaging();
        }

        try {
            $this->startEventState($staging);

            $this->getAdapter()->beginTransaction('enterprise_staging');
            $this->_beforeRun($staging);
            $this->_run($staging);
            $this->_afterRun($staging);
            $this->getAdapter()->commitTransaction('enterprise_staging');

            $message = $this->getEventStateStatuses()->complite;
            $message = Mage::helper('enterprise_staging')
                ->__($message);
            //$this->endEventState($staging, $message);
        } catch (Exception $e) {
            $this->getAdapter()->rollbackTransaction('enterprise_staging');

            $message = $this->getEventStateStatuses()->processing;
            
            $message = Mage::helper('enterprise_staging')
                ->__($message);
            
            $this->endEventState($staging, $message, $e, true);

            throw new Enterprise_Staging_Exception($e);
        }

        return $this;
    }
    
    /**
     * Process configured(additional) action before main Run
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * 
     * @return Enterprise_Staging_Model_Staging_State_Abstract 
     */
    protected function _beforeRun(Enterprise_Staging_Model_Staging $staging)
    {
        $staging->setStatus(Enterprise_Staging_Model_Staging_Config::STATUS_PROCESSING);
        $this->_runExtendActions('before', $staging);
        return $this;
    }

    abstract protected function _run(Enterprise_Staging_Model_Staging $staging);

    /**
     * Process configured(additional) action after main Run
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * 
     * @return Enterprise_Staging_Model_Staging_State_Abstract 
     */
    protected function _afterRun(Enterprise_Staging_Model_Staging $staging)
    {
        $staging->setStatus(Enterprise_Staging_Model_Staging_Config::STATUS_COMPLETE);
        $this->_runExtendActions('after', $staging);
        return $this;
    }

    /**
     * Process action Set
     *
     * @param string $nodeName
     * @param Enterprise_Staging_Model_Staging $staging 
     * 
     * @return Enterprise_Staging_Model_Staging_State_Abstract 
     */
    protected function _runExtendActions($nodeName, $staging)
    {   
        if ($config = $this->getConfig($nodeName)) {
            foreach ($config->children() AS $action) {
                $this->_runExtendAction($action, $staging);
            }
        }
        return $this;
    }
    
    /**
     * Process direct Action
     *
     * @param Varien_Simplexml_Element $action
     * @param Enterprise_Staging_Model_Staging $staging 
     * 
     * @return Enterprise_Staging_Model_Staging_State_Abstract 
     */
    protected function _runExtendAction($action, $staging)
    {
        
        $instance = null;
        
        $stateRegistryCode = "staging/" . $this->getEventStateCode() . "/".$action->class;
        $instance = Mage::registry($stateRegistryCode);
        
        if (!is_object($instance)) {
            switch ($action->type) {
                case "singleton" :
                    $instance = Mage::getSingleton($action->class);
                    break;
                default:
                    $instance = Mage::getModel($action->class);
                    break;                        
            }

            Mage::register($stateRegistryCode, $instance);
        }            
            
        if (is_object($instance)) {
            $method = (string) $action->method;
            if (!empty($method)) {
                $instance->$method($this, $staging);
            }                
        }
        return $this;
    }

    final public function next()
    {
        if ($this->getNextStateName()) {
            return $this->getScenario()->stateFactory($this->getNextStateName());
        } else {
            return false;
        }
    }
    
    public function setEventStateCode($code)
    {
        $this->_eventStateCode = $code;

        return $this;
    }

    public function getEventStateCode()
    {
        if (is_null($this->_eventStateCode)) {
            throw new Enterprise_Staging_Exception('State code is not defined.');
        }
        return $this->_eventStateCode;
    }

    public function setEventStateLabel($label)
    {
        $this->_eventStateLabel = $label;

        return $this;
    }

    public function getEventStateLabel()
    {
        if (is_null($this->_eventStateLabel)) {
            throw new Enterprise_Staging_Exception('State label is not defined.');
        }
        return $this->_eventStateLabel;
    }

    public function setEventStateStatuses($statuses)
    {
        $this->_eventStateStatuses = $statuses;

        return $this;
    }

    public function getEventStateStatuses()
    {
        if (is_null($this->_eventStateStatuses)) {
            throw new Enterprise_Staging_Exception('State statuses label is not defined.');
        }
        return $this->_eventStateStatuses;
    }

    public function setNextStateName($name)
    {
        $this->_nextStateName = $name;

        return $this;
    }

    public function getNextStateName()
    {
        if (is_null($this->_adapter)) {
            throw new Enterprise_Staging_Exception('Next state is not defined.');
        }
        return $this->_nextStateName;
    }

    public function check()
    {
        return true;
    }

    public function setAdapter(Enterprise_Staging_Model_Staging_Adapter_Abstract $adapter)
    {
        $this->_adapter = $adapter;
        return $this;
    }

    public function getAdapter()
    {
        if (is_null($this->_adapter)) {
            throw new Enterprise_Staging_Exception('Adapter is not setted.');
        }
        return $this->_adapter;
    }

    public function setScenario(Enterprise_Staging_Model_Staging_Scenario $scenario)
    {
        $this->_scenario = $scenario;
        return $this;
    }
    
    /**
     * Set config element
     *
     * @param Varien_Simplexml_Element $simpleXml
     */
    public function setConfig($simpleXml)
    {
        $this->_config = $simpleXml;
        return $this;        
    }
    
    /**
     * Get config node value
     *
     * @param empty
     * @return Varien_Simplexml_Element _config 
     * 
     * @param string $node (Config Node Name)
     * @return string Node Value  
     */
    public function getConfig($node = null)
    {
        if (is_null($node)) {
            return $this->_config;
        } else {
            $node = (string) $node;
            return $this->_config->$node;
        }
    }

    /**
     * Retrieve current scenario object
     *
     * @return object Enterprise_Staging_Model_Staging_Scenario
     */
    public function getScenario()
    {
        if (is_null($this->_scenario)) {
            throw new Enterprise_Staging_Exception('Scenario is not setted.');
        }
        return $this->_scenario;
    }

    /**
     * Set staging instance
     *
     * @param   mixed $staging
     * @return  Enterprise_Staging_Model_Staging_State_Abstract
     */
    public function setStaging($staging)
    {
        $this->_staging = $staging;

        return $this;
    }

    /**
     * Retrieve staging instance
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (is_object($this->_staging)) {
            return $this->_staging;
        }
        $_staging = Mage::registry('staging');
        if ($_staging && !is_null($this->_staging) && $_staging->getId() == (int) $this->_staging) {
            return $_staging;
        } else {
            if (is_int($this->_staging)) {
                $this->_staging = Mage::getModel('enterprise_staging/staging')
                    ->load((int)$this->_staging);
            } else {
                $this->_staging = false;
            }
        }
        return $this->_staging;
    }

    public function setWebsite(Enterprise_Staging_Model_Staging_Type_Website $website)
    {
        $this->_website = $website;
        return $this;
    }

    public function getWebsite()
    {
        if (is_null($this->_website)) {
            throw new Enterprise_Staging_Exception('Website is not specified.');
        }
        return $this->_website;
    }

    public function setGroup(Enterprise_Staging_Model_Staging_Type_Store_Group $group)
    {
        $this->_group = $group;
        return $this;
    }

    public function getGroup()
    {
        if (is_null($this->_group)) {
            throw new Enterprise_Staging_Exception('Store Group is not specified.');
        }
        return $this->_group;
    }

    public function setStore(Enterprise_Staging_Model_Staging_Type_Store $store)
    {
        $this->_store = $store;
        return $this;
    }

    public function getStore()
    {
        if (is_null($this->_store)) {
            throw new Enterprise_Staging_Exception('Store is not specified.');
        }
        return $this->_store;
    }



    public function startEventState($staging = null, $message = null, $log = null)
    {
/*
        if (!$this->_addToEventHistory) {
            return $this;
        }

        if (is_null($staging)) {
            $staging = $this->getStaging();
        }

        $eventStateCode = $this->getEventStateCode();

        if (is_null($message)) {
            $message = Mage::helper('enterprise_staging')
                ->__('%s event was started', $this->getEventStateLabel());
        }

        $staging->addEvent($eventStateCode,
            Enterprise_Staging_Model_Staging_Config::STATE_HOLDED,
            Enterprise_Staging_Model_Staging_Config::STATUS_HOLDED,
            $message,
            $log
        );
*/
        return $this;
    }

    public function endEventState($staging = null, $message = null, $log = null, $isError = false)
    {
        if (!$this->_addToEventHistory) {
            return $this;
        }

        if (is_null($staging)) {
            $staging = $this->getStaging();
        }

        $eventStateCode = $this->getEventStateCode();

        if (is_null($message)) {
            $message = Mage::helper('enterprise_staging')
                ->__('%s event was finished', $this->getEventStateLabel());
        }

        if ($isError) {
            $state  = Enterprise_Staging_Model_Staging_Config::STATE_HOLDED;
            $status = Enterprise_Staging_Model_Staging_Config::STATUS_HOLDED;
        } else {
            $state  = Enterprise_Staging_Model_Staging_Config::STATE_COMPLETE;
            $status = Enterprise_Staging_Model_Staging_Config::STATUS_COMPLETE;
        }

        $eventName = $this->getEventStateLabel();

        $staging->addEvent($eventStateCode, $state, $status, $eventName, $message, $log);

        return $this;
    }

    /**
     * Retrieve item resource adapter instance
     *
     * @param Varien_Simplexml_Element $itemXmlConfig
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    public function getItemAdapterInstanse($itemXmlConfig)
    {
        if (!$itemXmlConfig->code) {
            return $this;
        }
        $adapterModelName = (string) $itemXmlConfig->adapter;
        if (!$adapterModelName) {
            $adapterModelName = 'enterprise_staging/staging_adapter_item_abstract';
        }
        $adapter = Mage::getSingleton($adapterModelName);
        if ($adapter) {
            $adapter->setEventStateCode($this->getEventStateCode());
            return $adapter;
        } else {
            throw new Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Wrong item adapter model: %s', $adapterModelName));
        }

    }
}