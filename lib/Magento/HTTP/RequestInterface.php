<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_HTTP
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Magento HTTP Request Interface
 *
 * @category   Magento
 * @package    Magento_HTTP
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\HTTP;

/**
 * Class RequestInterface
 *
 * @package Magento\HTTP
 */
interface RequestInterface
{
    /**
     * Retrieve HTTP HOST
     *
     * @return string
     */
    public function getHttpHost();

    /**
     * Retrieve a member of the $_GET superglobal
     *
     * If no $key is passed, returns the entire $_GET array.
     *
     * @param  string $key
     * @param  mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getQuery($key = null, $default = null);

    /**
     * Set GET values
     *
     * @param  string|array $spec
     * @param  null|mixed $value
     * @return self
     */
    public function setQuery($spec, $value = null);

    /**
     * Returns the REQUEST_URI taking into account
     * platform differences between Apache and IIS
     *
     * @return string
     */
    public function getRequestUri();

    /**
     * Set the REQUEST_URI on which the instance operates
     *
     *
     * @param  string $requestUri
     * @return self
     */
    public function setRequestUri($requestUri = null);

    /**
     * Get request string
     *
     * @return string
     */
    public function getRequestString();

    /**
     * Get base url
     *
     * @return string
     */
    public function getBaseUrl();

    /**
     * Set the base URL of the request; i.e., the segment leading to the script name
     *
     * E.g.:
     * - /admin
     * - /myapp
     * - /subdir/index.php
     *
     * @param  mixed $baseUrl
     * @return self
     */
    public function setBaseUrl($baseUrl = null);

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * @return boolean
     */
    public function isXmlHttpRequest();

    /**
     * Check is Request from AJAX
     *
     * @return boolean
     */
    public function isAjax();

    /**
     * Is https secure request
     *
     * @return boolean
     */
    public function isSecure();

    /**
     * Set the PATH_INFO string
     * Set the ORIGINAL_PATH_INFO string
     *
     * @param string|null $pathInfo
     * @return self
     */
    public function setPathInfo($pathInfo = null);

    /**
     * Returns everything between the BaseUrl and QueryString.
     *
     * @return string
     */
    public function getPathInfo();

    /**
     * Specify new path info
     *
     * @param   string $pathInfo
     * @return  self
     */
    public function rewritePathInfo($pathInfo);

    /**
     * Returns ORIGINAL_PATH_INFO.
     *
     * @return string
     */
    public function getOriginalPathInfo();

    /**
     * Everything in REQUEST_URI before PATH_INFO not including the filename
     *
     * @return string
     */
    public function getBasePath();

    /**
     * Set the base path for the URL
     *
     * @param  string|null $basePath
     * @return self
     */
    public function setBasePath($basePath = null);

    /**
     * Get distro base url
     *
     * @return string
     */
    public function getDistroBaseUrl();

    /**
     * Retrieve a member of the $_FILES super global
     *
     * @param  string $key
     * @param  mixed $default Default value to use if key not found
     * @return mixed
     */
    public function getFiles($key = null, $default = null);

    /**
     * Return the method by which the request was made
     *
     * @return string
     */
    public function getMethod();

    /**
     * Retrieve a member of the $_POST superglobal
     *
     * If no $key is passed, returns the entire $_POST array.
     *
     * @param  string $key
     * @param  mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getPost($key = null, $default = null);

    /**
     * Set a member of the $_POST superglobal
     *
     * @param string|array $key
     * @param mixed $value
     *
     * @return self
     */
    public function setPost($key, $value = null);

    /**
     * Was the request made by POST?
     *
     * @return boolean
     */
    public function isPost();

    /**
     * Was the request made by GET?
     *
     * @return boolean
     */
    public function isGet();

    /**
     * Was the request made by PUT?
     *
     * @return boolean
     */
    public function isPut();

    /**
     * Was the request made by DELETE?
     *
     * @return boolean
     */
    public function isDelete();

    /**
     * Was the request made by HEAD?
     *
     * @return boolean
     */
    public function isHead();

    /**
     * Was the request made by OPTIONS?
     *
     * @return boolean
     */
    public function isOptions();

    /**
     * Return the value of the given HTTP header. Pass the header name as the
     * plain, HTTP-specified header name. Ex.: Ask for 'Accept' to get the
     * Accept header, 'Accept-Encoding' to get the Accept-Encoding header.
     *
     * @param  string $header HTTP header name
     * @return string|false HTTP header value, or false if not found
     */
    public function getHeader($header);

    /**
     * Get the request URI scheme
     *
     * @return string
     */
    public function getScheme();

    /**
     * Get the client's IP addres
     *
     * @param  boolean $checkProxy
     * @return string
     */
    public function getClientIp($checkProxy = true);

    /**
     * Is this a Flash request?
     *
     * @return boolean
     */
    public function isFlashRequest();

    /**
     * Retrieve a member of the $_COOKIE super global
     *
     * @param  string $key
     * @param  mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getCookie($key = null, $default = null);

    /**
     * Retrieve a member of the $_SERVER super global
     *
     * @param  string $key
     * @param  mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getServer($key = null, $default = null);

    /**
     * Retrieve a member of the $_ENV super global
     *
     * @param  string $key
     * @param  mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getEnv($key = null, $default = null);
}
