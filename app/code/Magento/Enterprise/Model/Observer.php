<?php
/**
 * {license_notice}
 *
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
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @param \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(\Magento\Backend\Model\Auth\Session $authSession)
    {
        $this->_authSession = $authSession;
    }

    /**
     * Set hide survey question to session
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function setHideSurveyQuestion($observer)
    {
        $this->_authSession->setHideSurveyQuestion(true);
        return $this;
    }
}
