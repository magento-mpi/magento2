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
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_TmtApi_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * @var string Wsdl
     */
    public $wsdlUrl;

    /**
     * @var string Login for soap service
     */
    public $login;

    /**
     * @var string Password for soap service
     */
    public $password;

    /**
     * @var Saas_Mage_TmtApi_Api
     */
    public $apiInstance;

    /**
     * Inits wsdl, login, password and creates instance of basic class Saas_Mage_TmtApi_Api
     */
    public function initBasicVars()
    {
        if (empty($this->wsdlUrl)) {
            $this->wsdlUrl =  $this->getConfigHelper()->getAreaBaseUrl('tmt_api');
        }
        $config = $this->getConfigHelper()->getConfigAreas();
        if (empty($this->login)) {
            $this->login = $config['tmt_api']['login'];
        }
        if (empty($this->password)) {
            $this->password = $config['tmt_api']['password'];
        }
        if (empty($this->apiInstance)) {
            $this->apiInstance = new Saas_Mage_TmtApi_Api($this->wsdlUrl, $this->login, $this->password);
        }
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
        if (empty($data)) {
            $this->fail('Empty data for creating tenant');
        }
        $this->initBasicVars();
        try {
            return $this->apiInstance->createTenant($data);
        } catch (RuntimeException $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * Check if domain already exist
     *
     * @param string $domain
     * @return int|null
     */
    public function checkDomainExists($domain)
    {
        if (empty($domain)) {
            $this->fail('Empty parameter');
        }
        $this->initBasicVars();

        return $this->apiInstance->checkDomainExists($domain);
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
        if (empty($tenantId)) {
            $this->fail('Tenant Id is empty');
        }
        $this->initBasicVars();
        try {
            $result = $this->apiInstance->deleteTenant($tenantId);
            return $result;
        } catch (RuntimeException $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * Wait till tenant becomes active
     *
     * @param string $tenantId
     * @param int $timeout in ms
     * @return bool
     */
    public function waitTenantBecomesActive($tenantId, $timeout = null)
    {
        if (empty($tenantId)) {
            $this->fail('Tenant Id is empty');
        }
        if (empty($timeout)) {
            $timeout = $this->getBrowserTimeout();
        }
        $this->initBasicVars();

        return $this->apiInstance->waitTenantBecomesActive($tenantId, $timeout);
    }
}
