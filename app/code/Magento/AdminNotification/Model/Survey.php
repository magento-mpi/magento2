<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * AdminNotification survey model
 *
 * @category   Magento
 * @package    Magento_AdminNotification
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdminNotification_Model_Survey
{
    const SURVEY_URL = 'www.magentocommerce.com/instsurvey.html';

    /**
     * @var string
     */
    protected $_flagCode  = 'admin_notification_survey';

    /**
     * @var Magento_Core_Model_Flag
     */
    protected $_flagModel = null;

    /**
     * @var Magento_Core_Model_FlagFactory
     */
    protected $_flagFactory;

    /**
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @param Magento_Core_Model_FlagFactory $flagFactory
     * @param Magento_Core_Controller_Request_Http $request
     */
    public function __construct(
        Magento_Core_Model_FlagFactory $flagFactory,
        Magento_Core_Controller_Request_Http $request
    ) {
        $this->_request = $request;
        $this->_flagFactory = $flagFactory;
    }

    /**
     * Check if survey url valid (exists) or not
     *
     * @return bool
     */
    public function isSurveyUrlValid()
    {
        $curl = new Magento_HTTP_Adapter_Curl();
        $curl->setConfig(array('timeout'   => 5))
            ->write(Zend_Http_Client::GET, $this->getSurveyUrl(), '1.0');
        $response = $curl->read();
        $curl->close();

        if (Zend_Http_Response::extractCode($response) == 200) {
            return true;
        }
        return false;
    }

    /**
     * Return survey url
     *
     * @return string
     */
    public function getSurveyUrl()
    {
        $host = $this->_request->isSecure() ? 'https://' : 'http://';
        return $host . self::SURVEY_URL;
    }

    /**
     * Return core flag model
     *
     * @return Magento_Core_Model_Flag
     */
    protected function _getFlagModel()
    {
        if ($this->_flagModel === null) {
            $this->_flagModel = $this->_flagFactory->create(
                array('data' => array('flag_code' => $this->_flagCode)))
                ->loadSelf();
        }
        return $this->_flagModel;
    }

    /**
     * Check if survey question was already asked
     * or survey url was viewed during installation process
     *
     * @return boolean
     */
    public function isSurveyViewed()
    {
        $flagData = $this->_getFlagModel()->getFlagData();
        if (isset($flagData['survey_viewed']) && $flagData['survey_viewed'] == 1) {
            return true;
        }
        return false;
    }

    /**
     * Save survey viewed flag in core flag
     *
     * @param boolean $viewed
     */
    public function saveSurveyViewed($viewed)
    {
        $flagData = $this->_getFlagModel()->getFlagData();
        if (is_null($flagData)) {
            $flagData = array();
        }
        $flagData = array_merge($flagData, array('survey_viewed' => (bool)$viewed));
        $this->_getFlagModel()->setFlagData($flagData)->save();
    }
}
