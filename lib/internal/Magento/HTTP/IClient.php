<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for different HTTP clients
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\HTTP;

interface IClient
{
    /**
     * Set request timeout
     * @param int $value
     * @return void
     */
    function setTimeout($value);
    
    
    /**
     * Set request headers from hash
     * @param array $headers
     * @return void
     */
    function setHeaders($headers);
    
    /**
     * Add header to request 
     * @param string $name
     * @param string $value
     * @return void
     */
    function addHeader($name, $value);
    
    
    /**
     * Remove header from request
     * @param string $name
     * @return void
     */
    function removeHeader($name);


    /**
     * Set login credentials
     * for basic auth.
     * @param string $login
     * @param string $pass
     * @return void
     */
    function setCredentials($login, $pass);
    
    /**
     * Add cookie to request 
     * @param string $name
     * @param string $value
     * @return void
     */
    function addCookie($name, $value);

    /**
     * Remove cookie from request
     * @param string $name
     * @return void
     */
    function removeCookie($name);
    
    /**
     * Set request cookies from hash
     * @param array $cookies
     * @return void
     */ 
    function setCookies($cookies);

    /**
     * Remove cookies from request
     *
     * @return void
     */
    function removeCookies();

    /**
     * Make GET request
     * @param string full uri
     * @return array
     */
    function get($uri);

    /**
     * Make POST request
     * @param string $uri full uri
     * @param array $params POST fields array
     * @return void
     */ 
    function post($uri, $params);
    
    /**
     * Get response headers
     * @return array
     */ 
    function getHeaders();
    
    /**
     * Get response body
     * @return string
     */
    function getBody(); 
    
    /**
     * Get response status code
     * @return int
     */
    function getStatus();
    
    /**
     * Get response cookies (k=>v) 
     * @return array
     */
    function getCookies();
    
    /**
     * Set additional option
     * @param string $key
     * @param string $value
     * @return void
     */
    function setOption($key, $value);

    /**
     * Set additional options
     * @param array $arr
     * @return void
     */
    function setOptions($arr);
}
