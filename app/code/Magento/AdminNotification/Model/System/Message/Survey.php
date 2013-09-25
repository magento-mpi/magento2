<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdminNotification_Model_System_Message_Survey
    implements Magento_AdminNotification_Model_System_MessageInterface
{
    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var Magento_AdminNotification_Model_Survey
     */
    protected $_survey;

    /**
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param Magento_AuthorizationInterface $authorization
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param Magento_AdminNotification_Model_Survey $survey
     */
    public function __construct(
        Magento_Backend_Model_Auth_Session $authSession,
        Magento_AuthorizationInterface $authorization,
        Magento_Core_Model_UrlInterface $urlBuilder,
        Magento_AdminNotification_Model_Survey $survey
    ) {
        $this->_authorization = $authorization;
        $this->_authSession = $authSession;
        $this->_urlBuilder = $urlBuilder;
        $this->_survey = $survey;
    }

    /**
     * Return survey url
     *
     * @return string
     */
    public function getSurveyUrl()
    {
        return $this->_survey->getSurveyUrl();
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
            || false == $this->_authorization->isAllowed(null)
            || $this->_survey->isSurveyViewed()
            || false == $this->_survey->isSurveyUrlValid()
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
        $params = array(
            'actionLink' => array(
                'event' => 'surveyYes',
                'eventData' => array(
                    'surveyUrl' => $this->_survey->getSurveyUrl(),
                    'surveyAction' => $this->_urlBuilder->getUrl('*/survey/index', array('_current' => true)),
                    'decision' => 'yes',
                ),
            ),
        );
        return __('We appreciate our merchants\' feedback. Please <a href="#" data-mage-init=%1>take our survey</a> and tell us about features you\'d like to see in Magento.', json_encode($params, JSON_FORCE_OBJECT));
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return Magento_AdminNotification_Model_System_MessageInterface::SEVERITY_MAJOR;
    }
}
