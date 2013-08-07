<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_License
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * License data helper
 *
 * @category   Magento
 * @package    Magento_License
 */
class Magento_License_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * It stores information about whether the extension uploaded php "Ioncube Loader"
     * 
     * @var bool
     */
    protected $_isIoncubeLoaded = null;

    /**
     * It stores information about whether the the application is encoded
     *
     * @var bool
     */
    protected $_isIoncubeEncoded = null;

    /**
     * It stores information about license properties if application is encoded
     *
     * @var bool
     */
    protected $_ioncubeLicenseProperties = null;

    /**
     * Get info: is run the extension php "ioncube Loader" or not. 
     * So, calculate it and stored in local variable of self class if it absent.
     * 
     * @return bool
     */
    public function isIoncubeLoaded(){
        if(null === $this->_isIoncubeLoaded) {
            $this->_isIoncubeLoaded = extension_loaded('ionCube Loader');
        }

        return $this->_isIoncubeLoaded;
    }

    /**
     * Get info: is application encoded by "ioncube Encoder" or not.
     * So, calculate it and stored in local variable of self class if it absent.
     *
     * @return bool
     */
    public function isIoncubeEncoded(){
        if(null === $this->_isIoncubeEncoded && $this->isIoncubeLoaded()) {
            $this->_isIoncubeEncoded = ioncube_file_is_encoded();
        }
        
        return $this->_isIoncubeEncoded;
    }

    /**
     * Get license proerties of encoded application.
     * So, calculate it and stored in local variable of self class if it absent.
     *
     * @return array Associative array consisting of license properties. Each value in the associative array retrieved by this API function is itself an array with two values: the license property value itself, and a boolean value signifies whether the property is enforced. The return value of this function is FALSE if the calling file is not encoded or has no license file.
     */
    public function getIoncubeLicenseProperties(){
        if($this->isIoncubeEncoded()) {
            $this->_ioncubeLicenseProperties = ioncube_license_properties();
        }

        return $this->_ioncubeLicenseProperties;
    }

}
