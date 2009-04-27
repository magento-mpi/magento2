<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @license    http://www.magentocommerce.com/license/enterprise-edition
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
     * State Xml config
     *
     * @var Varien_Simplexml_Element
     */
    protected $_config;

    /**
     * Event State Code
     *
     * @var string
     */
    protected $_eventStateCode;

    /**
     * Event State Label
     *
     * @var string
     */
    protected $_eventStateLabel;

    /**
     * Event State status message
     *
     * @var string
     */
    protected $_eventStateStatuses;

    /**
     * Catalog index flag
     * @var string
     */
    protected $_catalogIndexFlag = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_read  = Mage::getSingleton('core/resource')->getConnection('staging_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('staging_write');
    }

    /**
     * Run state process
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_State_Abstract
     */
    final public function run($staging = null)
    {
        if (is_null($staging)) {
            $staging = $this->getStaging();
        }

        try {
            if (!$staging->hasData('state_exception')) {
                $this->getAdapter()->beginTransaction('enterprise_staging');
                $this->_beforeRun($staging);
                $this->_run($staging);
                $this->getAdapter()->commitTransaction('enterprise_staging');
            }
        } catch (Exception $e) {
            $this->getAdapter()->rollbackTransaction('enterprise_staging');
            $staging->setData('state_exception', $e);
        }

        $this->_afterRun($staging);

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
        $class = (string) $action->class;

        $stateRegistryCode = "staging/" . $this->getEventStateCode() . "/".$class;
        $instance = Mage::registry($stateRegistryCode);

        if (!is_object($instance)) {
            $instance = Mage::getSingleton($class);
            Mage::register($stateRegistryCode, $instance);
        }

        if (is_object($instance)) {
            $method = (string) $action->method;
            if (!empty($method)) {
                $instance->{$method}($this, $staging);
            }
        }
        return $this;
    }

    /**
     * Execute next state of scenario
     *
     * @return object/false
     */
    final public function next()
    {
        if ($this->getNextStateName()) {
            return $this->getScenario()->stateFactory($this->getNextStateName());
        } else {
            return false;
        }
    }

    /**
     * Set state code
     *
     * @param string $code
     * @return Enterprise_Staging_Model_Staging_State_Abstract
     */
    public function setEventStateCode($code)
    {
        $this->_eventStateCode = $code;

        return $this;
    }

    /**
     * Return state code
     *
     * @return string
     */
    public function getEventStateCode()
    {
        if (is_null($this->_eventStateCode)) {
            throw new Enterprise_Staging_Exception('State code is not defined.');
        }
        return $this->_eventStateCode;
    }

    /**
     * Set Event state label as attribute
     *
     * @param string $label
     * @return Enterprise_Staging_Model_Staging_State_Abstract
     */
    public function setEventStateLabel($label)
    {
        $this->_eventStateLabel = $label;

        return $this;
    }

    /**
     * get state label text
     *
     * @return string
     */
    public function getEventStateLabel()
    {
        if (is_null($this->_eventStateLabel)) {
            throw new Enterprise_Staging_Exception('State label is not defined.');
        }
        return $this->_eventStateLabel;
    }

    /**
     * Set evet status message
     *
     * @param string $statuses
     * @return Enterprise_Staging_Model_Staging_State_Abstract
     */
    public function setEventStateStatuses($statuses)
    {
        $this->_eventStateStatuses = $statuses;

        return $this;
    }

    /**
     * get event status message
     *
     * @return string
     */
    public function getEventStateStatuses()
    {
        if (is_null($this->_eventStateStatuses)) {
            throw new Enterprise_Staging_Exception('State statuses label is not defined.');
        }
        return $this->_eventStateStatuses;
    }

    /**
     * Set next state name
     *
     * @param string $name
     * @return Enterprise_Staging_Model_Staging_State_Abstract
     */
    public function setNextStateName($name)
    {
        $this->_nextStateName = $name;

        return $this;
    }

    public function getEventStateStatusLabel($status)
    {
        $statuses = $this->getEventStateStatuses();
        if ($statuses) {
            if ($statusNode = $statuses->{$status}) {
                $status = (string) $statusNode->label;
                return Mage::helper('enterprise_staging')->__($status);
            }
        }
        return $status;;
    }

    /**
     * get next state name
     *
     * @return string
     */
    public function getNextStateName()
    {
        if (is_null($this->_nextStateName)) {
            throw new Enterprise_Staging_Exception('Next state is not defined.');
        }
        return $this->_nextStateName;
    }

    /**
     * check
     *
     * @return Enterprise_Staging_Model_Staging_State_Abstract
     */
    public function check()
    {
        return true;
    }

    /**
     * Get staging adapter
     *
     * @return Enterprise_Staging_Model_Staging_Adapter_Abstract
     */
    public function getAdapter()
    {
        if (is_null($this->_adapter)) {
            $this->_adapter = Mage::getModel('enterprise_staging/staging_adapter');
        }
        return $this->_adapter;
    }

    /**
     * Declare stenario model
     *
     * @param Enterprise_Staging_Model_Staging_Scenario $scenario
     * @return Enterprise_Staging_Model_Staging_State_Abstract
     */
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
            $adapter->setConfig($itemXmlConfig);
            return $adapter;
        } else {
            throw new Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Wrong item adapter model: %s', $adapterModelName));
        }

    }
}
