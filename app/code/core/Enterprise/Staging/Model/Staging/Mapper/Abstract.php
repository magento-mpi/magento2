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

abstract class Enterprise_Staging_Model_Staging_Mapper_Abstract extends Varien_Object
{
    /**
     * Staging instance id
     *
     * @var mixed
     */
    protected $_staging;

    /**
     * Staging type config data
     *
     * @var mixed
     */
    protected $_config;

    public function __construct()
    {
        $this->_read  = Mage::getSingleton('core/resource')->getConnection('staging_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('staging_write');
    }

    /**
     * Specify staging instance
     *
     * @param   mixed $staging
     * @return  Enterprise_Staging_Model_Staging_Mapper_Abstract
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
     * Setting specified config
     *
     * @param mixed $config
     * @return Enterprise_Staging_Model_Staging_Type_Abstract
     */
    public function setConfig($config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Retrieve Staging config
     * @param string $key
     * @return unknown
     */
    public function getConfig($key = null)
    {
        $false = false;
        if (is_null($key)) {
            return $this->_config;
        } else {
            if (isset($this->_config[$key])) {
                return $this->_config[$key];
            } else {
                return $false;
            }
        }
    }
}