<?php
/**
 * Test client for REST API testing.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\TestCase\Webapi\Adapter;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Webapi\Model\Rest\Config;

class Rest implements \Magento\TestFramework\TestCase\Webapi\AdapterInterface
{
    /** @var \Magento\Webapi\Model\Config */
    protected $_config;

    /** @var \Magento\Integration\Model\Oauth\Consumer */
    protected static $_consumer;

    /** @var \Magento\Integration\Model\Oauth\Token */
    protected static $_token;

    /** @var string */
    protected static $_consumerKey;

    /** @var string */
    protected static $_consumerSecret;

    /** @var string */
    protected static $_verifier;

    /** @var \Magento\TestFramework\TestCase\Webapi\Adapter\Rest\CurlClient */
    protected $curlClient;

    /** @var string */
    protected $defaultStoreCode;

    /**
     * Initialize dependencies.
     */
    public function __construct()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $this->_config = $objectManager->get('Magento\Webapi\Model\Config');
        $this->curlClient = $objectManager->get('Magento\TestFramework\TestCase\Webapi\Adapter\Rest\CurlClient');
        $this->defaultStoreCode = Bootstrap::getObjectManager()
            ->get('Magento\Framework\StoreManagerInterface')
            ->getStore()
            ->getCode();
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function call($serviceInfo, $arguments = array())
    {
        $resourcePath = '/' . $this->defaultStoreCode . $this->_getRestResourcePath($serviceInfo);
        $httpMethod = $this->_getRestHttpMethod($serviceInfo);
        //Get a valid token
        $accessCredentials = \Magento\TestFramework\Authentication\OauthHelper::getApiAccessCredentials();
        /** @var $oAuthClient \Magento\TestFramework\Authentication\Rest\OauthClient */
        $oAuthClient = $accessCredentials['oauth_client'];
        $urlFormEncoded = false;
        // we're always using JSON
        $authHeader = array();
        $restServiceInfo = $serviceInfo['rest'];
        if (array_key_exists('token', $restServiceInfo)) {
            $authHeader = $oAuthClient->buildBearerTokenAuthorizationHeader($restServiceInfo['token']);
        } else {
            $authHeader = $oAuthClient->buildOauthAuthorizationHeader(
                $this->curlClient->constructResourceUrl($resourcePath),
                $accessCredentials['key'],
                $accessCredentials['secret'],
                ($httpMethod == 'PUT' || $httpMethod == 'POST') && $urlFormEncoded ? $arguments : array(),
                $httpMethod
            );
        }
        $authHeader = array_merge($authHeader, ['Accept: application/json', 'Content-Type: application/json']);
        switch ($httpMethod) {
            case Config::HTTP_METHOD_GET:
                $response = $this->curlClient->get($resourcePath, array(), $authHeader);
                break;
            case Config::HTTP_METHOD_POST:
                $response = $this->curlClient->post($resourcePath, $arguments, $authHeader);
                break;
            case Config::HTTP_METHOD_PUT:
                $response = $this->curlClient->put($resourcePath, $arguments, $authHeader);
                break;
            case Config::HTTP_METHOD_DELETE:
                $response = $this->curlClient->delete($resourcePath, $authHeader);
                break;
            default:
                throw new \LogicException("HTTP method '{$httpMethod}' is not supported.");
        }
        if (defined('GENERATE_REST_DOCUMENTATION') && GENERATE_REST_DOCUMENTATION) {
            $this->generateDocumentation($httpMethod, $resourcePath, $arguments, $response);
        }

        return $response;
    }

    /**
     * Generate documentation based on request-response data during REST requests.
     *
     * @param string $httpMethod
     * @param string $resourcePath
     * @param array $arguments
     * @param array $response
     */
    protected function generateDocumentation($httpMethod, $resourcePath, $arguments, $response)
    {
        $arguments = json_encode($arguments, JSON_PRETTY_PRINT);
        $response = json_encode($response, JSON_PRETTY_PRINT);
        $varDir = realpath(__DIR__ . '/../../../../../..') . '/var';
        $documentationDir = $varDir . '/log/rest-documentation/';
        $debugBackTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $pathToFile = $documentationDir;
        foreach ($debugBackTrace as $traceItem) {
            /** Test invocation trace item is the only item which has 3 elements, other trace items have 5 elements */
            if (count($traceItem) == 3) {
                /** Remove 'test' prefix from method name, e.g. testCreate => create */
                $fileName = lcfirst(substr($traceItem['function'], 4));
                /** Remove 'Test' suffix from test class name */
                $pathToFile .= str_replace('\\', '/', substr($traceItem['class'], 0, -4)) . '/';
                $resource = preg_replace('#/\w*/V\d+/(.*)#', '${1}', $resourcePath);
                $resource = str_replace('/', '-',$resource);
                break;
            }
        }
        if (!isset($fileName)) {
            $fileName = 'unclassified';
        }
        if (!file_exists($pathToFile)) {
            if (!mkdir($pathToFile, 0755, true)) {
                throw new \RuntimeException('Unable to create missing directory for REST documentation generation');
            }
        }
        $filePath = $pathToFile . $fileName . '.html';
        $resourcePath = urldecode($resourcePath);
        $lowerCaseResource = strtolower($resource);
        $lowerCaseMethod = strtolower($httpMethod);
        $beginningHtml = <<<HTML
<div class="col-xs-9" role="main">
    <div class="bs-docs-section">
HTML;
        $headingHtml = <<<HTML
        <h2 class="api2" id="$lowerCaseResource">$resource</h2>
        <h3 class="api3" id="$lowerCaseMethod-$lowerCaseResource">$httpMethod $resourcePath</h3>
        <h4 class="api4">Request</h4>
HTML;
        $responseHtml = <<<HTML
        <h4 class="api4" id=”$lowerCaseResource-response>Response</h4>
HTML;
        $endHtml = <<<HTML
<h3 class="api3" id="products-parameters">Request and response parameters</h3>
        <table class="docutils field-list" frame="void" rules="none"  width="400">
            <colgroup>
                <col width="35%" class="field-name">
                <col  width="65%" class="field-body">
            </colgroup>
            <tbody valign="top">
            <tr class="field-odd field">
                <th class="field-name">Request parameters:</th>
                <td class="field-body">
                    <ul class="first last simple">
                        <li><strong>sku</strong> (<em>string</em>) – TBD.</li>
                        <li><strong>name</strong> (<em>string</em>) – TBD.</li>
                        <li><strong>visibility</strong> (<em>int</em>) – TBD.</li>
                        <li><strong>type_id</strong> (<em>string</em>) – TBD.</li>
                        <li><strong>price</strong> (<em>float</em>) – TBD.</li>
                        <li><strong>status</strong> (<em>int</em>) – TBD.</li>
                        <li><strong>custom_attributes</strong> (<em>dict</em>) – TBD.</li>
                    </ul>
                </td>
            </tr>
            </tbody>
        </table>
        <table class="docutils field-list" frame="void" rules="none"  width="400">
            <colgroup>
                <col width="35%" class="field-name">
                <col  width="65%" class="field-body">
            </colgroup>
            <tbody valign="top">
            <tr class="field-odd field">
                <th class="field-name">Response attributes:</th>
                <td class="field-body">
                    <ul class="first last simple">
                        <li><strong>sku</strong> (<em>string</em>) – TBD.</li>
                    </ul>
                </td>
            </tr>
            </tbody>
        </table>
        <h3 class="api3" id="products-responses">Response codes</h3>
        <table class="docutils field-list" frame="void" rules="none" width="400">
            <colgroup>
                <col  width="35%" class="field-name">
                <col  width="65%" class="field-body">
            </colgroup>
            <tbody valign="top">
            <tr class="field-odd field">
                <th class="field-name">Normal response codes:</th>
                <td class="field-body">
                    <ul class="first last simple">
                        <li><strong>200</strong> – Success.</li>
                        <li><strong>201</strong> – Success.</li>
                    </ul>
                </td>
            </tr>
            </tbody>
        </table>
        <table class="docutils field-list" frame="void" rules="none" width="400">
            <colgroup>
                <col  width="35%" class="field-name">
                <col  width="65%" class="field-body">
            </colgroup>
            <tbody valign="top">
            <tr class="field-odd field">
                <th class="field-name">Error response codes:</th>
                <td class="field-body">
                    <ul class="first last simple">
                        <li><strong>404</strong> – Something went wrong.</li>
                        <li><strong>501</strong> – Something else went wrong.</li>
                    </ul>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
HTML;
        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);
            $fileContent = str_replace($endHtml, '', $fileContent);
            $content = "$fileContent\n<pre>\n{$arguments}\n</pre>\n$responseHtml\n<pre>\n{$response}\n</pre>\n$endHtml";
            file_put_contents($filePath, $content, FILE_APPEND);
        } else {
            if ($resourcePath && $arguments && $response) {
                if (!is_writable(dirname($filePath))) {
                    throw new \RuntimeException('Directory for documentation generation is not writable.');
                }
                $content = "$beginningHtml\n $headingHtml \n<pre>\n{$arguments}\n</pre>\n$responseHtml\n<pre>\n{$response}\n</pre>\n$endHtml";
                file_put_contents($filePath, $content, FILE_APPEND);
            }
        }
    }

    /**
     * Retrieve REST endpoint from $serviceInfo array and return it to the caller.
     *
     * @param array $serviceInfo
     * @return string
     * @throws \Exception
     */
    protected function _getRestResourcePath($serviceInfo)
    {
        if (isset($serviceInfo['rest']['resourcePath'])) {
            $resourcePath = $serviceInfo['rest']['resourcePath'];
        }
        if (!isset($resourcePath)) {
            throw new \Exception("REST endpoint cannot be identified.");
        }
        return $resourcePath;
    }

    /**
     * Retrieve HTTP method to be used in REST request.
     *
     * @param array $serviceInfo
     * @return string
     * @throws \Exception
     */
    protected function _getRestHttpMethod($serviceInfo)
    {
        if (isset($serviceInfo['rest']['httpMethod'])) {
            $httpMethod = $serviceInfo['rest']['httpMethod'];
        }
        if (!isset($httpMethod)) {
            throw new \Exception("REST HTTP method cannot be identified.");
        }
        return $httpMethod;
    }
}
