<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Installer data model
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Installer_Data extends Magento_Object
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
     * @return Mage_Install_Model_Installer_Data
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
