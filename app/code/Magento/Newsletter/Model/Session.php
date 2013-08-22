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
 * Newsletter session model
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Newsletter_Model_Session extends Magento_Core_Model_Session_Abstract
{
    /**
     * Class constructor. Initialize session namespace
     *
     * @param string $sessionName
     */
    public function __construct($sessionName = null)
    {
        $this->init('newsletter', $sessionName);
    }

    public function addError($message)
    {
        $this->setErrorMessage($message);
        return $this;
    }

    public function addSuccess($message)
    {
        $this->setSuccessMessage($message);
        return $this;
    }

    public function getError()
    {
        $message = $this->getErrorMessage();
        $this->unsErrorMessage();
        return $message;
    }

    public function getSuccess()
    {
        $message = $this->getSuccessMessage();
        $this->unsSuccessMessage();
        return $message;
    }
}
