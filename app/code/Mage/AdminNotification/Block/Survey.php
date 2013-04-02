<?php
/**
 * Adminhtml AdminNotification survey question block
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_AdminNotification_Block_Survey extends Mage_Backend_Block_Template
{
    /**
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * @var Mage_Core_Model_Authorization
     */
    protected $_authorization;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Backend_Model_Auth_Session $authSession
     * @param Mage_Core_Model_Authorization $authorization
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Backend_Model_Auth_Session $authSession,
        Mage_Core_Model_Authorization $authorization,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_authSession = $authSession;
        $this->_authorization = $authorization;
    }

    /**
     * Check whether survey question can show
     *
     * @return boolean
     */
    public function canShow()
    {
        if ($this->_authSession->getHideSurveyQuestion()
            || !$this->_authorization->isAllowed(Mage_Backend_Model_Acl_Config::ACL_RESOURCE_ALL)
            || Mage_AdminNotification_Model_Survey::isSurveyViewed()
            || !Mage_AdminNotification_Model_Survey::isSurveyUrlValid()
        ) {

            return false;
        }
        return true;
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
}
