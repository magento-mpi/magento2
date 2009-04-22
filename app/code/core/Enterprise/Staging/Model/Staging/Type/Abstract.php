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
 * Abstract model for staging type implementation
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Staging_Model_Staging_Type_Abstract
{
    /**
     * Staging type instance id
     *
     * @var string
     */
    protected $_typeId;

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

    /**
     * Specify staging instance
     *
     * @param   mixed $staging
     * @return  Enterprise_Staging_Model_Staging_Type_Abstract
     */
    public function setStaging($staging)
    {
        $this->_staging = $staging;

        return $this;
    }

    /**
     * Specify type identifier
     *
     * @param   string $typeId
     * @return  Enterprise_Staging_Model_Staging_Type_Abstract
     */
    public function setTypeId($typeId)
    {
        $this->_typeId = $typeId;

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
