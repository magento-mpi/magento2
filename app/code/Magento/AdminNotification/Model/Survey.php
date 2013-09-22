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
namespace Magento\AdminNotification\Model;

class Survey
{
    const SURVEY_URL = 'www.magentocommerce.com/instsurvey.html';

    /**
     * @var string
     */
    protected $_flagCode  = 'admin_notification_survey';

    /**
     * @var \Magento\Core\Model\Flag
     */
    protected $_flagModel = null;

    /**
     * @var \Magento\Core\Model\FlagFactory
     */
    protected $_flagFactory;

    /**
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_request;

    /**
     * @param \Magento\Core\Model\FlagFactory $flagFactory
     * @param \Magento\Core\Controller\Request\Http $request
     */
    public function __construct(
        \Magento\Core\Model\FlagFactory $flagFactory,
        \Magento\Core\Controller\Request\Http $request
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
        $curl = new \Magento\HTTP\Adapter\Curl();
        $curl->setConfig(array('timeout'   => 5))
            ->write(\Zend_Http_Client::GET, $this->getSurveyUrl(), '1.0');
        $response = $curl->read();
        $curl->close();

        if (\Zend_Http_Response::extractCode($response) == 200) {
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
     * @return \Magento\Core\Model\Flag
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
