<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Installer data model
 */
class Magento_Install_Model_Installer_Data extends Magento_Object
{

    /**
     * Errors array
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Add error
     *
     * @param string $error
     * @return Magento_Install_Model_Installer_Data
     */
    public function addError($error)
    {
        $this->_errors[] = $error;
        return $this;
    }

    /**
     * Get all errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

}
