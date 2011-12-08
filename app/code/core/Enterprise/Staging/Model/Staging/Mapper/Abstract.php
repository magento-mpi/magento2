<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
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
     * Constructor
     *
     */
    public function __construct()
    {
        $this->_read  = Mage::getSingleton('Mage_Core_Model_Resource')->getConnection('staging_read');
        $this->_write = Mage::getSingleton('Mage_Core_Model_Resource')->getConnection('staging_write');
    }

    /**
     * Declare staging instance
     *
     * @param   Enterprise_Staging_Model_Staging $staging
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
        if ($this->_staging instanceof Enterprise_Staging_Model_Staging) {
            return $this->_staging;
        } elseif (is_null($this->_staging)) {
            $_staging = Mage::registry('staging');
            if ($_staging && $this->_staging &&  ($_staging->getId() == $this->_staging)) {
                $this->_staging = $_staging;
            } else {
                if (is_int($this->_staging)) {
                    $this->_staging = Mage::getModel('Enterprise_Staging_Model_Staging')
                        ->load($this->_staging);
                } else {
                    $this->_staging = false;
                }
            }
        }

        return $this->_staging;
    }
}
