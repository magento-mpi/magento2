<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enterprise general observer
 *
 */
namespace Magento\Enterprise\Model;

class Observer
{
    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * @param Magento_Backend_Model_Auth_Session $authSession
     */
    public function __construct(
        Magento_Backend_Model_Auth_Session $authSession
    ) {
        $this->_authSession = $authSession;
    }

    /**
     * Set hide survey question to session
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Enterprise\Model\Observer
     */
    public function setHideSurveyQuestion($observer)
    {
        $this->_authSession->setHideSurveyQuestion(true);
        return $this;
    }
}
