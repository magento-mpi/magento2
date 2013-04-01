<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_System_Message_Survey implements Mage_Backend_Model_System_MessageInterface
{
    /**
     * @var Mage_Core_Model_Factory_Helper
     */
    protected $_helperFactory;

    /**
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * @var Mage_Core_Model_Authorization
     */
    protected $_authorization;

    /**
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Backend_Model_Auth_Session $authSession
     * @param Mage_Core_Model_Authorization $authorization
     */
    public function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Backend_Model_Auth_Session $authSession,
        Mage_Core_Model_Authorization $authorization
    ) {
        $this->_helperFactory = $helperFactory;
        $this->_authorization = $authorization;
        $this->_authSession = $authSession;
    }

    /**
     * Return survey url
     *
     * @return string
     */
    public function getSurveyUrl()
    {
        return Mage_AdminNotification_Model_Survey::getSurveyUrl();
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return md5('survey' . $this->getSurveyUrl());
    }

    /**
     * Check whether survey question can show
     *
     * @return bool
     */
    public function isDisplayed()
    {
        if ($this->_authSession->getHideSurveyQuestion()
            || false == $this->_authorization->isAllowed(Mage_Backend_Model_Acl_Config::ACL_RESOURCE_ALL)
            || Mage_AdminNotification_Model_Survey::isSurveyViewed()
            || false == Mage_AdminNotification_Model_Survey::isSurveyUrlValid()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        return $this->_helperFactory->get('Mage_Backend_Helper_Data')->__('We appreciate our merchants\' feedback, please <a href="#" onclick="surveyAction(\'yes\'); return false;">take our survey</a> to provide insight on the features you would like included in Magento. <a href="#" onclick="surveyAction(\'no\'); return false;">Remove this notification</a>');
    }

    /**
     * Retrieve problem management url
     *
     * @return string|null
     */
    public function getLink()
    {
        return null;
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return Mage_Backend_Model_System_MessageInterface::SEVERITY_MAJOR;
    }
}
