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
 * @category    Mage
 * @package     Mage_Captcha
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    protected $_formId;
    // 0 = don't limit
    protected $_lifetime = 0;
    protected $_ignoreTtl = false;

    /**
     * Captcha session constructor
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        if (!isset($params['formId'])) {
            Mage::throwException('formId is mandatory');
        }
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
        return parent::setData($this->_getFullKey($key), serialize($data));
    }

    /**
     * Reads from session
     *
     * @param string $key
     * @param bool   $clear
     * @return mixed
     */
    public function getData($key = '', $clear = false)
    {
        $data = parent::getData($this->_getFullKey($key), $clear);
        if (!is_string($data) || !preg_match('/^a:\d+:\{/', $data)) {
            // Data has not been set via self::setData(), not serialized
            return $data;
        }
        $data = unserialize($data);
        if (!isset($data['expires']) || !isset($data['data'])) {
            return null;
        }
        $lifetimeExceeded = (time() >= $data['expires']);
        if (!$this->_ignoreTtl && $lifetimeExceeded) {
            // Timed out
            $this->unsetData($key);
            return null;
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
        $this->_ignoreTtl = true;
        $data = $this->getData($key, $clear);
        $this->_ignoreTtl = false;
        return $data;
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
     * Data TTL
     *
     * @param int $seconds
     * @return void
     */
    public function setLifetime($seconds)
    {
        $this->_lifetime = $seconds;
    }

    /**
     * Returns data TTL
     *
     * @return int
     */
    public function getLifeTime()
    {
        return $this->_lifetime;
    }
}
