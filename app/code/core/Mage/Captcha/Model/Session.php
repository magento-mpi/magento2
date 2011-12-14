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
