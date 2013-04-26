<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_TmtApi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Additional class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_TmtApi_Api
{
    /**
     * Wsdl url
     *
     * @var string $wsdlUrl
     */
    public $wsdlUrl;

    /**
     * Login to soap service
     *
     * @var string $login
     */
    public $login;

    /**
     * Password for soap service
     *
     * @var string $password
     */
    public $password;

    /**
     * Constructor
     *
     * @param string $wsdlUrl
     * @param string $login
     * @param string $password
     */
    public function __construct($wsdlUrl, $login, $password)
    {
        $this->wsdlUrl = $wsdlUrl;
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * Create tenant
     *
     * @param array $data
     * @return string Response from service
     * @throws RuntimeException
     */
    public function createTenant($data = array())
    {
        $client = $this->getClient();
        $token = $this->getToken($client);
        $url = explode('/', $this->wsdlUrl);
        $url = explode('.', $url[2]);
        $url[0] = $data['domain'];
        $domain = implode('.', $url);
        $existResponse = $this->checkDomainExists($token, $domain, $client);
        if (!empty($existResponse)) {
            throw new RuntimeException('Domain, you are trying to create, already exists');
        }
        $response = $client->createTenant($token, $data);
        if ($this->isSuccessful($response)) {
            return $response['response'];
        }
        throw new RuntimeException('Domain, you are trying to create, already exists'
            . $response['response'][0]['description']);
    }

    /**
     * Check if domain already exist
     *
     * @param string $token
     * @param string $domain
     * @param SoapClient $client
     * @return int|null
     */
    public function checkDomainExists($token, $domain, SoapClient $client)
    {
        $response = $client->checkDomain($token, array('domain' => $domain));
        return ($this->isSuccessful($response)) ? $response['response']['exists'] : null;
    }

    /**
     * Delete tenant
     *
     * @param string $tenantId
     * @return bool
     * @throws RuntimeException
     */
    public function deleteTenant($tenantId)
    {
        $client = $this->getClient();
        $token = $this->getToken($client);
        $tenantInfo = $client->tenantInfo($token, array('tenant_id' => $tenantId));
        $exist = $this->isSuccessful($tenantInfo);
        $message = "Could not delete tenant with id '$tenantId'. Tenant does not exist";
        if ($exist) {
            $response = $client->deleteTenant($token, array('tenant_id' => $tenantId));
            if ($this->isSuccessful($response)) {
                return true;
            } else {
                $message = "Could not delete tenant with id '$tenantId'. " . $response['response'][0]['description'];
            }
        }
        throw new RuntimeException($message);
    }

    /**
     * Wait till tenant becomes active
     *
     * @param string $tenantId
     * @param int $timeout in ms
     * @return bool
     */
    public function waitTenantBecomesActive($tenantId, $timeout)
    {
        $client = $this->getClient();
        $token = $this->getToken($client);
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            try {
                $tenantInfo = $client->tenantInfo($token, array('tenant_id' => $tenantId));
                if ($this->isSuccessful($tenantInfo)) {
                    if ($tenantInfo['response']['status'] == 0) {
                        return true;
                    }
                }
                usleep(500000);
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Gets the token
     *
     * @param SoapClient $client
     * @return null
     */
    private function getToken(SoapClient $client)
    {
        $token = $client->login($this->login, $this->password);
        return ($this->isSuccessful($token)) ? $token['response']['token'] : null;
    }

    /**
     * Returns true if request is successful and false otherwise
     *
     * @param array $response
     * @return bool
     */
    private function isSuccessful($response)
    {
        if (isset($response['status']) && $response['status'] == 'success') {
            return true;
        }
        return false;
    }

    /**
     * Create instance of SoapClient and returns it
     *
     * @return null|SoapClient
     */
    private function getClient()
    {
        try {
            $client = new SoapClient($this->wsdlUrl);
        } catch (Exception $e) {
            return null;
        }
        return $client;
    }
}
