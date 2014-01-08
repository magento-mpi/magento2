<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Gdata
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Shopping Item manager model
 *
 * @category    Magento
 * @package     Magento_Gdata
 */
namespace Magento\Gdata\Gshopping;

class Content extends \Zend_Gdata
{
    /**
     * Authentication service name for Google Shopping
     */
    const AUTH_SERVICE_NAME = 'structuredcontent';

    /**
     * Google Merchant account ID
     * @var string
     */
    protected $_accountId;

    /**
     * Debug flag
     *
     * @var bool
     */
    protected $_debug = false;

    /**
     * Log adapter instance
     *
     * @var null|object
     */
    protected $_logAdapter = null;

    /**
     * Log method name in log adapter
     *
     * @var string
     */
    protected $_logAdapterLogAction;

    /**
     * Array with namespaces for entry
     *
     * @var array
     */
    public static $namespaces = array(
        array('sc', 'http://schemas.google.com/structuredcontent/2009', 1, 0),
        array('scp', 'http://schemas.google.com/structuredcontent/2009/products', 1, 0),
        array('app', 'http://www.w3.org/2007/app', 1, 0),
    );

    /**
     * Create object
     *
     * @param \Zend_Http_Client $client (optional) The HTTP client to use when
     *          when communicating with the Google Apps servers.
     * @param string $applicationId The identity of the app in the form of Company-AppName-Version
     */
    public function __construct($client = null, $accountId = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        $this->_accountId = $accountId;
        parent::__construct($client, $applicationId);
        $this->_httpClient->setParameterPost('service', self::AUTH_SERVICE_NAME);
    }

    /**
     * Retrieve entry object
     *
     * @param mixed $location The location for the feed, as a URL or Query
     * @return \Magento\Gdata\Gshopping\Entry
     */
    public function getItem($location = null)
    {
        if ($location === null) {
            throw new \Zend_Gdata_App_InvalidArgumentException('Location must not be null');
        }

        $uri = ($location instanceof \Zend_Gdata_Query) ? $location->getQueryUrl() : $location;

        $entry = $this->getEntry($uri, 'Magento\Gdata\Gshopping\Entry')
            ->setService($this);
        return $entry;
    }


    /**
     * Insert an entry
     *
     * @param \Magento\Gdata\Gshopping\Entry $entry The Content entry to upload
     * @param boolean $dryRun Flag for the 'dry-run' parameter
     * @return \Magento\Gdata\Gshopping\Entry
     */
    public function insertItem(\Magento\Gdata\Gshopping\Entry $entry, $dryRun = false)
    {
        $uri = $this->_getItemsUri();
        if ($dryRun) {
            $uri .= '?dry-run=true';
        }

        return $this->insertEntry($entry, $uri, 'Magento\Gdata\Gshopping\Entry');
    }

    /**
     * Update an entry
     *
     * @param \Magento\Gdata\Gshopping\Entry $entry The Content entry to be updated
     * @param boolean $dryRun Flag for the 'dry-run' parameter
     * @return \Magento\Gdata\Gshopping\Entry
     */
    public function updateItem(\Magento\Gdata\Gshopping\Entry $entry, $dryRun = false)
    {
        return $entry->save($dryRun);
    }

    /**
     * Delete an entry
     *
     * @param \Magento\Gdata\Gshopping\Entry $entry The Content entry to remove
     * @param boolean $dryRun Flag for the 'dry-run' parameter
     * @return \Magento\Gdata\Gshopping\Content Implements fluent interface
     */
    public function deleteItem(\Magento\Gdata\Gshopping\Entry $entry, $dryRun = false)
    {
        $entry->delete($dryRun);
        return $this;
    }

    /**
     * Create new entry
     *
     * @return Entry
     */
    public function newEntry()
    {
        $entry = new Entry();
        $entry->setService($this);
        return $entry;
    }

    /**
     * Create new item's query object
     *
     * @return ItemQuery
     */
    public function newItemQuery()
    {
        $itemQuery = new ItemQuery();
        $itemQuery->setFeedUri($this->_getItemsUri());
        return $itemQuery;
    }

    /**
     * Create new content extension object
     *
     * @param string $text
     * @param string $type
     * @param string $src
     * @return \Zend_Gdata_App_Extension_Content
     */
    public function newContent($text = null, $type = 'text', $src = null)
    {
        return new \Zend_Gdata_App_Extension_Content($text, $type, $src);
    }

    /**
     * Return URI for items manipulation
     *
     * @return string
     */
    protected function _getItemsUri()
    {
        return "https://content.googleapis.com/content/v1/$this->_accountId/items/products/generic";
    }

    /**
     * Performs a HTTP request using the specified method
     *
     * @param string $method The HTTP method for the request - 'GET', 'POST',
     *                       'PUT', 'DELETE'
     * @param string $url The URL to which this request is being performed
     * @param array $headers An associative array of HTTP headers
     *                       for this request
     * @param string $body The body of the HTTP request
     * @param string $contentType The value for the content type
     *                                of the request body
     * @param int $remainingRedirects Number of redirects to follow if request
     *                              s results in one
     * @return \Zend_Http_Response The response object
     */
    public function performHttpRequest($method, $url, $headers = null, $body = null, $contentType = null, $remainingRedirects = null)
    {
        try {
            $url .= '?warnings';
            $debugData = array(
                'method'                => $method,
                'url'                   => $url,
                'headers'               => $headers,
                'body'                  => $body,
                'content_type'          => $contentType,
                'remaining_redirects'   => $remainingRedirects
            );
            $result = parent::performHttpRequest($method, $url, $headers, $body, $contentType, $remainingRedirects);
            $debugData['response'] = $result;
            $this->debugData($debugData);
            return $result;
        } catch (\Zend_Gdata_App_HttpException $e) {
            $debugData['response'] = $e->getResponse();
            $this->debugData($debugData);
            throw new \Magento\Gdata\Gshopping\HttpException($e);
        }
    }

    /**
     * Log debug data
     *
     * @param mixed $debugData
     * @return \Magento\Gdata\Gshopping\Content
     */
    public function debugData($debugData)
    {
        if ($this->_debug && !is_null($this->_logAdapter)) {
            $method = $this->_logAdapterLogAction;
            $this->_logAdapter->$method($debugData);
        }
        return $this;
    }

    /**
     * Set debug flag
     *
     * @param bool $flag
     * @return \Magento\Gdata\Gshopping\Content
     */
    public function setDebug($flag)
    {
        $this->_debug = $flag;
        return $this;
    }

    /**
     * Set log adapter
     *
     * @param object $instance
     * @param string $method
     * @return \Magento\Gdata\Gshopping\Content
     */
    public function setLogAdapter($instance, $method)
    {
        if (method_exists($instance, $method)) {
            $this->_logAdapter = $instance;
            $this->_logAdapterLogAction = $method;
        }
        return $this;
    }
}
