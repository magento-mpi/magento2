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
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData;

    /**
     * @param Magento_Core_Model_Session_Context $context
     * @param Magento_Core_Helper_Data $coreData
     * @param array $data
     * @param string|null $sessionName
     */
    public function __construct(
        Magento_Core_Model_Session_Context $context,
        Magento_Core_Helper_Data $coreData,
        array $data = array(),
        $sessionName = null
    ) {
        $this->_coreData = $coreData;
        parent::__construct($context, $data);
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
            $this->setData('_form_key', $this->_coreData->getRandomString(16));
        }
        return $this->getData('_form_key');
    }
}
