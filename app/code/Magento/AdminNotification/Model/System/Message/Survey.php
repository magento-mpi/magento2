<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminNotification\Model\System\Message;

class Survey
    implements \Magento\AdminNotification\Model\System\MessageInterface
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Core\Model\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\AdminNotification\Model\Survey
     */
    protected $_survey;

    /**
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\AuthorizationInterface $authorization
     * @param \Magento\Core\Model\UrlInterface $urlBuilder
     * @param \Magento\AdminNotification\Model\Survey $survey
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\AuthorizationInterface $authorization,
        \Magento\Core\Model\UrlInterface $urlBuilder,
        \Magento\AdminNotification\Model\Survey $survey
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
        return \Magento\AdminNotification\Model\System\MessageInterface::SEVERITY_MAJOR;
    }
}
