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

    public function __construct()
    {
        $this->_read  = Mage::getSingleton('core/resource')->getConnection('staging_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('staging_write');
    }

    final public function next()
    {
        if ($this->getNextStateName()) {
            return $this->getScenario()->stateFactory($this->getNextStateName());
        } else {
            return false;
        }
    }

    final public function setNextStateName($name)
    {
        $this->_nextStateName = $name;

        return $this;
    }

    final public function getNextStateName()
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
     * Specify staging instance
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
}