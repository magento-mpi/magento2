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

/**
 * Enterprise Staging Scenario class
 *
 */
final class Enterprise_Staging_Model_Staging_Scenario
{
    private $_scenarioCode;

    private $_adapter;

    private $_staging;

    private $_state;

    /**
     * Build given scenario events
     *
     * @param unknown_type $scenario
     */
    function init($adapter, $scenarioCode)
    {
        $this->setAdapter($adapter);
        $this->setScenarioCode($scenarioCode);

        $initState = $this->stateFactory();

        if ($initState) {
            $this->setState($initState);
        }
        return $this;
    }

    final public function run()
    {
        $state = $this->getState();
        if ($state) {
            do {
                if ($state->check()) {
                    $state->run();
                }
            } while ($state = $state->next());
        }
        return $this;
    }

    function setAdapter($adapter)
    {
        $this->_adapter = $adapter;

        return $this;
    }

    function getAdapter()
    {
        return $this->_adapter;
    }

    function setScenarioCode($code)
    {
        $this->_scenarioCode = $code;

        return $this;
    }

    function getScenarioCode()
    {
        return $this->_scenarioCode;
    }

    function setState(Enterprise_Staging_Model_Staging_State_Abstract $state)
    {
        $this->_state = $state;

        return $this;
    }

    function getState()
    {
        return $this->_state;
    }

    /**
     * Specify staging instance
     *
     * @param   mixed $staging
     * @return  Enterprise_Staging_Model_Staging
     */
    public function setStaging($staging)
    {
        $this->_staging = $staging;
        return $this;
    }

    /**
     * Retrieve staging object
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (is_object($this->_staging)) {
            return $this->_staging;
        }
        /* TODO try to set staging_id instead whole staging object */
        $_staging = Mage::registry('staging');
        if ($_staging && $_staging->getId() == (int) $this->_staging) {
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
     * Staging state abstract factory
     *
     * @param   string  $stateCode
     * @param   bool    $singleton
     *
     * @return  object Enterprise_Staging_Model_Staging_State_Abstract
     */
    public function stateFactory($stateCode = 'prepare', $singleton = false)
    {
        $staging        = $this->getStaging();
        $scenarioCode   = $this->getScenarioCode();

        $path = 'type/' . $staging->getType() . '/scenaries/' . $scenarioCode . '/' . $stateCode;

        $stateConfig = Enterprise_Staging_Model_Staging_Config::getConfig($path);

        if (empty($stateConfig)) {
            return false;
        }

        $modelName      = (string) $stateConfig->model;
        if (empty($modelName)) {
            throw new Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Need to specify class name for state model') );
        }

        if ($singleton === true) {
            $model = Mage::getSingleton($modelName);
        } else {
            $model = Mage::getModel($modelName);
        }

        $nextModelName  = (string) $stateConfig->next;
        $model->setNextStateName($nextModelName);

        // TODO need to try to give current staging into models as an attribute
        $model->setStaging($staging->getId());

        $model->setAdapter($this->getAdapter());
        $model->setScenario($this);

        return $model;
    }
}