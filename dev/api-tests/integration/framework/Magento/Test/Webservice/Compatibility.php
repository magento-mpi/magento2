<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Webservice_Compatibility extends Magento_Test_Webservice
{
    /**
     * Get webservice configured at previous Magento API.
     *
     * @return Magento_Test_Webservice_Abstract
     */
    public function getPrevWebservice(){
        $options = array(
            'webservice_url' => TESTS_PREV_WEBSERVICE_URL,
            'api_user'       => TESTS_PREV_WEBSERVICE_USER,
            'api_key'        => TESTS_PREV_WEBSERVICE_APIKEY,
        );

        return $this->getWebService('previous', $options);
    }

    /**
     * Get webservice configured at current Magento API.
     *
     * @return Magento_Test_Webservice_Abstract
     */
    public function getCurrWebservice(){
        $options = array(
            'webservice_url' => TESTS_WEBSERVICE_URL,
            'api_user'       => TESTS_WEBSERVICE_USER,
            'api_key'        => TESTS_WEBSERVICE_APIKEY,
        );

        return $this->getWebService('current', $options);
    }

    /**
     * Call previous API resource
     *
     * @param string $path
     * @param array $params
     * @return array|string
     */
    public function prevCall($path, $params = array())
    {
        return $this->getPrevWebservice()->call($path, $params);
    }

    /**
     * Call current API resource
     *
     * @param string $path
     * @param array $params
     * @return array|string
     */
    public function currCall($path, $params = array())
    {
        return $this->getCurrWebservice()->call($path, $params);
    }
}
