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

/**
 * Enterprise Staging Scenario class
 *
 */
class Enterprise_Staging_Model_Staging_Scenario
{
    /**
     * Scenario Code
     *
     * @var string
     */
    private $_scenarioCode;

    /**
     * Staging model
     *
     * @var Enterprise_Staging_Model_Staging
     */
    private $_staging;

    /**
     * state model
     *
     * @var Enterprise_Staging_Model_Staging_State_Abstract
     */
    private $_state;

    /**
     * Init scenario first state
     *
     * @param string $scenarioCode
     * @return Enterprise_Staging_Model_Staging_Scenario
     */
    function init($scenarioCode, $firstState = null)
    {
        $this->setScenarioCode($scenarioCode);

        $initState = $this->stateFactory($firstState);

        if ($initState) {
            $this->setState($initState);
        }

        if (!Mage::registry($scenarioCode . "_event_start_time")) {
            $date = Mage::getModel('core/date')->gmtDate();
            Mage::register($scenarioCode . "_event_start_time", $date);
        }

        return $this;
    }

    /**
     * Run scenario
     *
     * @return Enterprise_Staging_Model_Staging_Scenario
     */
    final public function run()
    {
        $state = $this->getState();
        if ($state) {
            do {
                if ($state->check()) {
                    $state->run();
                }
            } while ($state = $state->next());

            $staging = $this->getStaging();
            if ($staging->hasData('state_exception')) {
                throw new Enterprise_Staging_Exception($staging->getData('state_exception'));
            }
        }
        return $this;
    }

    /**
     * set scenario code
     *
     * @param srting $code
     * @return Enterprise_Staging_Model_Staging_Scenario
     */
    function setScenarioCode($code)
    {
        $this->_scenarioCode = $code;

        return $this;
    }

    /**
     * get scenario code
     *
     * @return string
     */
    function getScenarioCode()
    {
        return $this->_scenarioCode;
    }

    /**
     * Enter description here...
     *
     * @param Enterprise_Staging_Model_Staging_State_Abstract $state
     * @return Enterprise_Staging_Model_Staging_Scenario
     */
    function setState(Enterprise_Staging_Model_Staging_State_Abstract $state)
    {
        $this->_state = $state;

        return $this;
    }

    /**
     * get state model
     *
     * @return Enterprise_Staging_Model_Staging_State_Abstract
     */
    function getState()
    {
        return $this->_state;
    }

    /**
     * Specify staging instance
     *
     * @param   mixed $staging
     * @return  Enterprise_Staging_Model_Staging_Scenario
     */
    public function setStaging($staging)
    {
        $this->_staging = $staging;
        return $this;
    }

    /**
     * Retrieve staging object
     *
     * @return  Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (is_object($this->_staging)) {
            return $this->_staging;
        }
        /* try to set staging_id instead whole staging object */
        $_staging = Mage::registry('staging');
        if ($_staging && !is_null($this->_staging) && $_staging->getId() == (int) $this->_staging) {
            return $_staging;
        } else {
            if (is_int($this->_staging)) {
                $this->_staging = Mage::getModel('enterprise_staging/staging')->load($this->_staging);
            } else {
                $this->_staging = false;
            }
        }

        return $this->_staging;
    }

    /**
     * Staging event state abstract factory
     *
     * @param   string  $stateCode
     * @param   boolean $singleton
     *
     * @return  object  Enterprise_Staging_Model_Staging_State_Abstract
     */
    public function stateFactory($stateCode = null, $singleton = false)
    {
        if (is_null($stateCode)) {
            $stateCode = 'prepare';
        }

        $staging        = $this->getStaging();
        $scenarioCode   = $this->getScenarioCode();

        $stagingType = $staging->getType();
        if (is_null($stagingType)) {
            $stagingType = Enterprise_Staging_Model_Staging_Config::DEFAULT_TYPE;
        }

        $path = 'type/' . $stagingType . '/scenaries/' . $scenarioCode . '/' . $stateCode;

        $stateConfig = Enterprise_Staging_Model_Staging_Config::getConfig($path);

        if (empty($stateConfig)) {
            return false;
        }

        $modelName      = (string) $stateConfig->model;
        if (empty($modelName)) {
            throw new Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Need to specify class name for state model') );
        }

        if ($singleton === true) {
            $state = Mage::getSingleton($modelName);
        } else {
            $state = Mage::getModel($modelName);
        }
        /* var $state Enterprise_Staging_Model_Staging_State_Abstract */

        $nextModelName      = (string) $stateConfig->next;
        $eventStateCode     = (string) $stateConfig->code;
        $eventStateLabel    = (string) $stateConfig->label;

        $state->setEventStateStatuses($stateConfig->status);
        $state->setConfig($stateConfig);
        $state->setEventStateCode($eventStateCode);
        $state->setEventStateLabel($eventStateLabel);
        $state->setNextStateName($nextModelName);
        $state->setStaging($staging);
        $state->setScenario($this);

        return $state;
    }
}
