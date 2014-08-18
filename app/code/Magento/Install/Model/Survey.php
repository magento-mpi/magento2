<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Installation survey model
 */
namespace Magento\Install\Model;

class Survey
{
    const SURVEY_URL = 'www.magentocommerce.com/instsurvey.html';

    /**
     * @var string
     */
    protected $_flagCode = 'install_survey';

    /**
     * @var \Magento\Framework\Flag
     */
    protected $_flagModel = null;

    /**
     * @var \Magento\Framework\FlagFactory
     */
    protected $_flagFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @param \Magento\Framework\FlagFactory $flagFactory
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\FlagFactory $flagFactory,
        \Magento\Framework\App\RequestInterface $request
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
        $curl = new \Magento\Framework\HTTP\Adapter\Curl();
        $curl->setConfig(array('timeout' => 5))->write(\Zend_Http_Client::GET, $this->getSurveyUrl(), '1.0');
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
     * @return \Magento\Framework\Flag
     */
    protected function _getFlagModel()
    {
        if ($this->_flagModel === null) {
            $this->_flagModel = $this->_flagFactory->create(
                array('data' => array('flag_code' => $this->_flagCode))
            )->loadSelf();
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
     * @return void
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
