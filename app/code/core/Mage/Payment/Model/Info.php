<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payment information model
 *
 * @category   Mage
 * @package    Mage_Payment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Payment_Model_Info extends Mage_Core_Model_Abstract
{
    /**
     * List of fields that has to be encrypted
     * Format: method_name => array(field1, field2, ... )
     *
     * @var array
     */
    protected $_encryptFields;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
         $this->_encryptFields = array(
             'ccsave' => array(
                 'cc_owner',
                 'cc_exp_year',
                 'cc_exp_month',
             ),
        );
        parent::__construct($data);
    }
    /**
     * Additional information container
     *
     * @var array
     */
    protected $_additionalInformation = -1;

    /**
     * Retrieve data
     *
     * @param   string $key
     * @param   mixed $index
     * @return  mixed
     */
    public function getData($key='', $index=null)
    {
        if ('cc_number'===$key) {
            if (empty($this->_data['cc_number']) && !empty($this->_data['cc_number_enc'])) {
                $this->_data['cc_number'] = $this->decrypt($this->getCcNumberEnc());
            }
        }
        if ('cc_cid'===$key) {
            if (empty($this->_data['cc_cid']) && !empty($this->_data['cc_cid_enc'])) {
                $this->_data['cc_cid'] = $this->decrypt($this->getCcCidEnc());
            }
        }
        return parent::getData($key, $index);
    }

    /**
     * Get CC Name
     * @return string
     */
    public function getCcOwner()
    {
        return $this->_getDecryptedData('cc_owner');
    }

    /**
     * Set CC Name
     * @param $value
     * @return Mage_Payment_Model_Info
     */
    public function setCcOwner($value)
    {
       $this->_setEncryptedData('cc_owner', $value);
        return $this;
    }

    /**
     * Get CC Expire Month
     * @return mixed
     */
    public function getCcExpMonth()
    {
        return $this->_getDecryptedData('cc_exp_month');
    }

    /**
     * Set CC Expire Month
     * @param $value
     * @return Mage_Payment_Model_Info
     */
    public function setCcExpMonth($value)
    {
       $this->_setEncryptedData('cc_exp_month', $value);
        return $this;
    }

    /**
     * Get CC Expire Year
     * @return mixed
     */
    public function getCcExpYear()
    {
        return $this->_getDecryptedData('cc_exp_year');
    }

    /**
     * Set CC Expire Year
     * @param $value
     * @return Mage_Payment_Model_Info
     */
    public function setCcExpYear($value)
    {
       $this->_setEncryptedData('cc_exp_year', $value);
        return $this;
    }

    /**
     * Get decrypted filed data
     * @param string $key field name
     * @return mixed
     */
    protected function _getDecryptedData($key)
    {
        $data = $this->getData($key);
        if (true == $this->_isEncryptedField($key)) {
            $data = $this->decrypt($data);
        }
        return $data;
    }

    /**
     * Set encrypted value
     *
     * @param string $key
     * @param mixed $value
     */
    protected function _setEncryptedData($key, $value)
    {
        if (false == $this->_isEncryptedField($key)) {
            $this->setData($key, $value);
        } else {
            $this->setData($key, $this->encrypt($value));
        }
    }

    /**
     * Check if specified field is encrypted
     *
     * @param string $key field name
     * @return bool
     */
    protected function _isEncryptedField($key)
    {
        if (false == isset($this->_encryptFields[$this->getData('method')])) {
            return false;
        }
        return in_array($key, $this->_encryptFields[$this->getData('method')]);
    }

    /**
     * Retrieve payment method model object
     *
     * @return Mage_Payment_Model_Method_Abstract
     * @throws Mage_Core_Exception
     */
    public function getMethodInstance()
    {
        if (!$this->hasMethodInstance()) {
            if ($this->getMethod()) {
                $instance = Mage::helper('Mage_Payment_Helper_Data')->getMethodInstance($this->getMethod());
                if ($instance) {
                    $instance->setInfoInstance($this);
                    $this->setMethodInstance($instance);
                    return $instance;
                }
            }
            Mage::throwException(Mage::helper('Mage_Payment_Helper_Data')->__('The requested Payment Method is not available.'));
        }

        return $this->_getData('method_instance');
    }

    /**
     * Encrypt data
     *
     * @param   string $data
     * @return  string
     */
    public function encrypt($data)
    {
        if ($data) {
            return Mage::helper('Mage_Core_Helper_Data')->encrypt($data);
        }
        return $data;
    }

    /**
     * Decrypt data
     *
     * @param   string $data
     * @return  string
     */
    public function decrypt($data)
    {
        if ($data) {
            return Mage::helper('Mage_Core_Helper_Data')->decrypt($data);
        }
        return $data;
    }

    /**
     * Additional information setter
     * Updates data inside the 'additional_information' array
     * or all 'additional_information' if key is data array
     *
     * @param string|array $key
     * @param mixed $value
     * @return Mage_Payment_Model_Info
     * @throws Mage_Core_Exception
     */
    public function setAdditionalInformation($key, $value = null)
    {
        if (is_object($value)) {
            Mage::throwException(Mage::helper('Mage_Sales_Helper_Data')->__('Payment disallow storing objects.'));
        }
        $this->_initAdditionalInformation();
        if (is_array($key) && is_null($value)) {
            $this->_additionalInformation = $key;
        } else {
            $this->_additionalInformation[$key] = $value;
        }
        return $this->setData('additional_information', $this->_additionalInformation);
    }

    /**
     * Getter for entire additional_information value or one of its element by key
     *
     * @param string $key
     * @return array|null|mixed
     */
    public function getAdditionalInformation($key = null)
    {
        $this->_initAdditionalInformation();
        if (null === $key) {
            return $this->_additionalInformation;
        }
        return isset($this->_additionalInformation[$key]) ? $this->_additionalInformation[$key] : null;
    }

    /**
     * Unsetter for entire additional_information value or one of its element by key
     *
     * @param string $key
     * @return Mage_Payment_Model_Info
     */
    public function unsAdditionalInformation($key = null)
    {
        if ($key && isset($this->_additionalInformation[$key])) {
            unset($this->_additionalInformation[$key]);
            return $this->setData('additional_information', $this->_additionalInformation);
        }
        $this->_additionalInformation = -1;
        return $this->unsetData('additional_information');
    }

    /**
     * Check whether there is additional information by specified key
     *
     * @param $key
     * @return bool
     */
    public function hasAdditionalInformation($key = null)
    {
        $this->_initAdditionalInformation();
        return null === $key
            ? !empty($this->_additionalInformation)
            : array_key_exists($key, $this->_additionalInformation);
    }

    /**
     * Make sure _additionalInformation is an array
     */
    protected function _initAdditionalInformation()
    {
        if (-1 === $this->_additionalInformation) {
            $this->_additionalInformation = $this->_getData('additional_information');
        }
        if (null === $this->_additionalInformation) {
            $this->_additionalInformation = array();
        }
    }
}
