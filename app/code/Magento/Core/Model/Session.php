<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core session model
 *
 * @todo extend from Magento_Core_Model_Session_Abstract
 *
 * @method null|bool getCookieShouldBeReceived()
 * @method Magento_Core_Model_Session setCookieShouldBeReceived(bool $flag)
 * @method Magento_Core_Model_Session unsCookieShouldBeReceived()
 */
class Magento_Core_Model_Session extends Magento_Core_Model_Session_Abstract
{
    /**
     * @param Magento_Core_Model_Session_Validator $validator
     * @param string $sessionName
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Session_Validator $validator,
        $sessionName = null,
        array $data = array()
    ) {
        parent::__construct($validator, $data);
        $this->init('core', $sessionName);
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string A 16 bit unique key for forms
     */
    public function getFormKey()
    {
        if (!$this->getData('_form_key')) {
            $this->setData('_form_key', Mage::helper('Magento_Core_Helper_Data')->getRandomString(16));
        }
        return $this->getData('_form_key');
    }
}
