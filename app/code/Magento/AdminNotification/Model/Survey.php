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
    protected static $_flagCode  = 'admin_notification_survey';
    protected static $_flagModel = null;

    const SURVEY_URL = 'www.magentocommerce.com/instsurvey.html';

    /**
     * Check if survey url valid (exists) or not
     *
     * @return boolen
     */
    public static function isSurveyUrlValid()
    {
        $curl = new \Magento\HTTP\Adapter\Curl();
        $curl->setConfig(array('timeout'   => 5))
            ->write(\Zend_Http_Client::GET, self::getSurveyUrl(), '1.0');
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
    public static function getSurveyUrl()
    {
        $host = \Mage::app()->getRequest()->isSecure() ? 'https://' : 'http://';
        return $host . self::SURVEY_URL;
    }

    /**
     * Return core flag model
     *
     * @return \Magento\Core\Model\Flag
     */
    protected static function _getFlagModel()
    {
        if (self::$_flagModel === null) {
            self::$_flagModel = \Mage::getModel('Magento\Core\Model\Flag',
                array('data' => array('flag_code' => self::$_flagCode)))
                ->loadSelf();
        }
        return self::$_flagModel;
    }

    /**
     * Check if survey question was already asked
     * or survey url was viewed during installation process
     *
     * @return boolean
     */
    public static function isSurveyViewed()
    {
        $flagData = self::_getFlagModel()->getFlagData();
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
    public static function saveSurveyViewed($viewed)
    {
        $flagData = self::_getFlagModel()->getFlagData();
        if (is_null($flagData)) {
            $flagData = array();
        }
        $flagData = array_merge($flagData, array('survey_viewed' => (bool)$viewed));
        self::_getFlagModel()->setFlagData($flagData)->save();
    }
}
