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

namespace Magento\Newsletter\Block;

class Subscribe extends \Magento\Core\Block\Template
{
    public function getSuccessMessage()
    {
        $message = \Mage::getSingleton('Magento\Newsletter\Model\Session')->getSuccess();
        return $message;
    }

    public function getErrorMessage()
    {
        $message = \Mage::getSingleton('Magento\Newsletter\Model\Session')->getError();
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
