<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\System\Config;

/**
 * Curl handler for saving customer address in admin
 *
 * @package Magento\Customer\Test\Handler\Curl
 */
class SaveCustomerWithAddress extends Curl
{
    /**
     * Url for saving data
     *
     * @var string
     */
    protected $saveUrl = '/customer/address/formPost/?nocookie=true';

    /**
     * Url for saving customer
     *
     * @var string
     */
    protected $saveCustomer = 'customer/account/createpost/?nocookie=true';

    /**
     * Url of new address form
     *
     * @var string
     */
    protected $addressNew = '/customer/address/new/?nocookie=true';

    /**
     * Form key
     *
     * @var string
     */
    protected $formKey;

    /**
     * Prepare POST data for creating customer request
     *
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        $curlData = array();
        foreach ($data as $key => $values) {
            $value = $this->getValue($values);
            if (null === $value) {
                continue;
            }
            $curlData[$key] = $value;
        }
        $curlData['success_url'] = '';
        $curlData['error_url'] = '';
        $curlData['default_billing'] = 1;
        $curlData['default_shipping'] = 1;

        return $curlData;
    }

    /**
     * Retrieve field value or return null if value does not exist
     *
     * @param array $values
     * @return null|mixed
     */
    protected function getValue($values)
    {
        if (!isset($values['value'])) {
            return null;
        }
        return isset($values['input_value']) ? $values['input_value'] : $values['value'];
    }

    /**
     * Execute handler
     *
     * @param Fixture $fixture
     * @return mixed
     */
    public function execute(Fixture $fixture = null)
    {
        /** @var \Magento\Customer\Test\Fixture\Customer $fixture */
        $address = $fixture->getDefaultBillingAddress();
        $fields = $this->prepareData($address->getData('fields'));
        $url = $_ENV['app_frontend_url'] . $this->saveUrl;
        $curl = $this->saveCustomer($fixture);
        $fields['form_key'] = $this->formKey;
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $fields);
        $response = $curl->read();
        $curl->close();

        return $response;
    }

    /**
     * Get from key from response
     *
     * @param string $response
     * @return string
     */
    protected function getFromKey($response)
    {
        preg_match('/input name="form_key" type="hidden" value="(\w+)"/', $response, $matches);
        $formKey = '';
        if (!empty($matches[1])) {
            $formKey = $matches[1];
        }
        return $formKey;
    }

    /**
     * Save new customer and get form key
     *
     * @param \Magento\Customer\Test\Fixture\Customer $fixture
     * @return CurlTransport
     */
    protected function saveCustomer(\Magento\Customer\Test\Fixture\Customer $fixture)
    {
        $data = $fixture->getData('fields');
        $fields = array();
        foreach ($data as $key => $field) {
            $fields[$key] = $field['value'];
        }
        $url = $_ENV['app_frontend_url'] . $this->saveCustomer;
        $curl = new CurlTransport();
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $fields);
        $curl->read();
        $urlForm = $_ENV['app_frontend_url'] . $this->addressNew;
        $curl->write(CurlInterface::GET, $urlForm, '1.0', array());
        $response = $curl->read();
        $this->formKey = $this->getFromKey($response);

        return $curl;
    }
}
