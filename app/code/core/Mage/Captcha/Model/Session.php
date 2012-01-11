<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Session implementation for captcha
 *
 * @category   Mage
 * @package    Mage_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Model_Session extends Mage_Core_Model_Session
{
    /**
     * Form id
     *
     * @var string
     */
    protected $_formId;
    /**
     * Life time
     *
     * @var int
     */
    protected $_lifetime = 0;
    /**
     * Captcha session constructor
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        if (!isset($params['formId'])) {
            throw new Exception('formId is mandatory');
        }
        $this->_lifetime = $params['lifetime'];
        $this->_formId = $params['formId'];
        $this->init('captcha');
    }

    /**
     * Returns form-unique key to be stored in session
     *
     * @param $key
     * @return string
     */
    protected function _getFullKey($key)
    {
        return $this->_formId . '_' . $key;
    }

    /**
     * Writes to session
     *
     * @param string $key
     * @param mixed  $value
     * @return Mage_Captcha_Model_Session
     */
    public function setData($key, $value = null)
    {
        $data = array('data' => $value, 'expires' => time() + $this->_lifetime);
        return parent::setData($this->_getFullKey($key), $data);
    }

    /**
     * Reads from session
     *
     * @param string $key
     * @param bool $clear
     * @param bool $ignoreTtl
     * @return mixed
     */
    public function getData($key = '', $clear = false, $ignoreTtl = false)
    {
        $data = parent::getData($this->_getFullKey($key), $clear);

        if (!isset($data['expires']) || !isset($data['data'])) {
            return null;
        }

        if (!$ignoreTtl && (time() >= $data['expires'])) {
            $this->unsetData($key);
            return null;
        }

        if($clear){
            $this->unsetData($key);
        }

        return $data['data'];
    }

    /**
     * Return data even if its TTL has expired
     *
     * @param string $key
     * @param bool   $clear
     * @return mixed
     */
    public function getDataIgnoreTtl($key, $clear = false)
    {
        return $this->getData($key, $clear, true);
    }

    /**
     * Removes from session
     *
     * @param string $key
     * @return Mage_Captcha_Model_Session
     */
    public function unsetData($key = '')
    {
        return parent::unsetData($this->_getFullKey($key));
    }

    /**
     * Set Form Id
     *
     * @param string $formId
     * @return Mage_Captcha_Model_Session
     */
    public function setFormId($formId)
    {
        $this->_formId = $formId;
        return $this;
    }

    /**
     * Get Form Id
     *
     * @return string
     */
    public function getFormId()
    {
        return $this->_formId;
    }
}
