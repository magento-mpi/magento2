<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core session model
 *
 * @todo extend from Mage_Core_Model_Session_Abstract
 *
 */
class Mage_Core_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct($data=array())
    {
        $name = isset($data['name']) ? $data['name'] : null;
        $this->init('core', $name);
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string A 16 bit unique key for forms
     */
    public function getFormKey()
    {
        if (!$this->getData('_form_key')) {
            $this->setData('_form_key', Mage::helper('Mage_Core_Helper_Data')->getRandomString(16));
        }
        return $this->getData('_form_key');
    }
}
