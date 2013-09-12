<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter subscribe block
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Newsletter_Block_Subscribe extends Magento_Core_Block_Template
{
    public function getSuccessMessage()
    {
        $message = Mage::getSingleton('Magento_Newsletter_Model_Session')->getSuccess();
        return $message;
    }

    public function getErrorMessage()
    {
        $message = Mage::getSingleton('Magento_Newsletter_Model_Session')->getError();
        return $message;
    }

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('newsletter/subscriber/new', array('_secure' => true));
    }
}
