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

class Enterprise_Staging_Model_Staging_Adapter_Website extends Enterprise_Staging_Model_Staging_Adapter_Abstract
{

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Scenario Create staging 
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_Adapter_Website
     */
    public function create(Enterprise_Staging_Model_Staging $staging)
    {
        try {
            $scenario = Mage::getModel('enterprise_staging/staging_scenario');
            $scenario->setStaging($staging);
            $scenario->init($this, 'create');
            $scenario->run();
        } catch (Exception $e) {
            $staging->saveEventHistory();
            throw new Enterprise_Staging_Exception($e);
        }
        $staging->saveEventHistory();
        return $this;
    }

    /**
     * Merge staging 
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_Adapter_Website
     */    
    public function merge(Enterprise_Staging_Model_Staging $staging)
    {
        try {
            $scenario = Mage::getModel('enterprise_staging/staging_scenario');
            $scenario->setStaging($staging);
            $scenario->init($this, 'merge');
            $scenario->run();
        } catch (Exception $e) {
            $staging->saveEventHistory();
            throw new Enterprise_Staging_Exception($e);
        }
        $staging->saveEventHistory();
        return $this;
    }

    /**
     * Rollback staging 
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_Adapter_Website
     */    
    public function rollback(Enterprise_Staging_Model_Staging $staging)
    {
        try {
            $scenario = Mage::getModel('enterprise_staging/staging_scenario');
            $scenario->setStaging($staging);
            $scenario->init($this, 'rollback');
            $scenario->run();
        } catch (Exception $e) {
            $staging->saveEventHistory();
            throw new Enterprise_Staging_Exception($e);
        }
        $staging->saveEventHistory();
        return $this;
    }

    /**
     * Check staging state 
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_Adapter_Website
     */    
    public function check(Enterprise_Staging_Model_Staging $staging)
    {
        try {
            $scenario = Mage::getModel('enterprise_staging/staging_scenario');
            $scenario->setStaging($staging);
            $scenario->init($this, 'check');
            $scenario->run();
        } catch (Exception $e) {
            $staging->saveEventHistory();
            throw new Enterprise_Staging_Exception($e);
        }
        $staging->saveEventHistory();
        return $this;
    }

    /**
     * Repair staging structure 
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_Adapter_Website
     */    
    public function repair(Enterprise_Staging_Model_Staging $staging)
    {
        try {
            $scenario = Mage::getModel('enterprise_staging/staging_scenario');
            $scenario->setStaging($staging);
            $scenario->init($this, 'repair');
            $scenario->run();
        } catch (Exception $e) {
            $staging->saveEventHistory();
            throw new Enterprise_Staging_Exception($e);
        }
        $staging->saveEventHistory();
        return $this;
    }

    /**
     * Copy staging structure 
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_Adapter_Website
     */    
    public function copy(Enterprise_Staging_Model_Staging $staging)
    {
        try {
            $scenario = Mage::getModel('enterprise_staging/staging_scenario');
            $scenario->setStaging($staging);
            $scenario->init($this, 'copy');
            $scenario->run();
        } catch (Exception $e) {
            $staging->saveEventHistory();
            throw new Enterprise_Staging_Exception($e);
        }
        $staging->saveEventHistory();
        return $this;
    }

    /**
     * Backup staging  
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Staging_Adapter_Website
     */    
    public function backup(Enterprise_Staging_Model_Staging $staging)
    {
        try {
            $scenario = Mage::getModel('enterprise_staging/staging_scenario');
            $scenario->setStaging($staging);
            $scenario->init($this, 'backup');
            $scenario->run();
        } catch (Exception $e) {
            $staging->saveEventHistory();
            throw new Enterprise_Staging_Exception($e);
        }
        $staging->saveEventHistory();
        return $this;
    }
}