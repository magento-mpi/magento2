<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * LoadTest spider
 *
 * @category   Mage
 * @package    Mage
 * @author     Victor Tihonchuk <victor@varien.com>
 */

class LoadTest_Object
{
    /**
     * Object attributes
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Setter/Getter underscore transformation cache
     *
     * @var array
     */
    protected static $_underscoreCache = array();

    /**
     * Set/Get attribute wrapper
     *
     * @param sting $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        switch (substr($method, 0, 3)) {
            case 'set':
                $key = $this->_underscore(substr($method, 3));
                return $this->setData($key, isset($args[0]) ? $args[0] : null);

            case 'get':
                $key = $this->_underscore(substr($method, 3));
                return $this->getData($key);

            case 'uns':
                $key = $this->_underscore(substr($method, 3));
                return $this->unsData($key);

            case 'has':
                $key = $this->_underscore(substr($method, 3));
                return isset($this->_data[$key]);

            default:
                throw new Exception('Invalid method '. $method);
        }
    }

    /**
     * Converts field names for setters and geters
     *
     * $this->setMyField($value) === $this->setData('my_field', $value)
     * Uses cache to eliminate unneccessary preg_replace
     *
     * @param string $name
     * @return string
     */
    protected function _underscore($name)
    {
        if (isset(self::$_underscoreCache[$name])) {
            return self::$_underscoreCache[$name];
        }
        $result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
        self::$_underscoreCache[$name] = $result;
        return $result;
    }

    /**
     * Add data to the object
     *
     * @param array $array
     * @return LoadTest_Object
     */
    public function addData(array $array)
    {
        foreach ($array as $k => $v) {
            $this->setData($k, $v);
        }
        return $this;
    }

    /**
     * Overwrite data in the object
     *
     * @param mixed $key
     * @param mixed $value
     * @return LoadTest_Object
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->_data = $key;
        }
        else {
            $this->_data[$key] = $value;
        }
        return $this;
    }

    /**
     * Unset data from the object
     *
     * @param mixed $key
     * @return LoadTest_Object
     */
    public function unsData($key = null)
    {
        if (is_null($key)) {
            $this->_data = array();
        }
        else {
            unset($this->_data[$key]);
        }
        return $this;
    }

    /**
     * Retrieves data from the object
     *
     * @param mixed $key
     * @return mixed
     */
    public function getData($key = null)
    {
        if (is_null($key)) {
            return $this->_data;
        }
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        }
        return null;
    }
}

class LoadTest_Url
{
    /**
     * Socket resource
     *
     * @var resource
     */
    protected $_handler;

    /**
     * Request data object
     *
     * @var LoadTest_Object
     */
    protected $_request;

    /**
     * Response data object
     *
     * @var LoadTest_Object
     */
    protected $_response;

    /**
     * XML object current after fetch or previous before fetch
     *
     * @var SimpleXMLElement
     */
    protected $_xml;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->_request = new LoadTest_Object();
        $this->_response = new LoadTest_Object();

        $this->getRequest()
            ->setHost()
            ->setPath('/')
            ->setMethod('GET')
            ->setPort(80)
            ->setTimeout(30)
            ->setGetData(new LoadTest_Object())
            ->setPostData(new LoadTest_Object())
            ->setCookieData(new LoadTest_Object());
    }

    /**
     * Get Request object
     *
     * @return LoadTest_Object
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get Response object
     *
     * @return LoadTest_Object
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Parse url string and set parametrs to the request object
     *
     * @param string $str
     * @return LoadTest_Url
     */
    public function parse($str)
    {
        if ($strpos = strpos($str, ' ')) {
            $url    = substr($str, 0, $strpos);
            $data   = substr($str, $strpos + 1);
        } else {
            $url    = $str;
            $data   = null;
        }

        if ($parsedUrl = parse_url($url)) {
            $this->getRequest()->setHost(isset($parsedUrl['host']) ? $parsedUrl['host'] : null);
            $this->getRequest()->setPath(isset($parsedUrl['path']) ? $parsedUrl['path'] : '/');
            if (isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $query);
                foreach ($query as $k => $v) {
                    $this->getRequest()->getGetData()->setData($k, $v);
                }
            }
            if (isset($parsedUrl['port'])) {
                $this->getRequest()->setPort($parsedUrl['port']);
            }
        }
        else {
            throw new Exception('Invalid URL ' . $url);
        }

        if ($data) {
            preg_match_all('/([A-Z]+)\:([\'|\\\"])(.*?[^\\\\])\\2.*?/', $data, $methods, PREG_SET_ORDER);

            $params = array();

            foreach ($methods as $value) {
                $method = trim(strtolower($value[1]));
                if (!$this->getRequest()->getData($method . '_data')) {
                    continue;
                }
                if ($method == 'post') {
                    $this->getRequest()->setMethod('POST');
                }
                $dataObject = $this->getRequest()->getData($method . '_data');

                $args = chop(trim($value[3]), ';') . ';';

                preg_match_all('/([A-Za-z0-9_]+)\=(.*?[^\\\\]);.*?/', $args, $match, PREG_SET_ORDER);

                foreach ($match as $arg) {
                    $k  = $arg[1];
                    $v  = $arg[2];
                    $v  = preg_replace_callback('/(\\\\)(.)/', array($this, '_parseEscapeChar'), $v);
                    $v  = preg_replace_callback('/(\{\{([a-zA-Z0-9_\.]+)\}\})/', array($this, '_parseValues'), $v);

                    $dataObject->setData($k, $v);
                }
            }
        }

        return $this;
    }

    /**
     * Callback method for replace escaped chars
     *
     * @param array $matches
     * @return string
     */
    protected function _parseEscapeChar($matches)
    {
        if (in_array($matches[2], array('r', 'n', 't'))) {
            eval('return "\\'.$matches[2].'";');
        }
        return $matches[2];
    }

    /**
     * Callback method for replace special xml variables
     * ex: response.attribute_set_id -> xpath:/loadtest/response/attribute_set_id
     *
     * @param array $matches
     * @return string
     */
    protected function _parseValues($matches)
    {
        $xpath = '/loadtest/' . str_replace('.', '/', $matches[2]);
        if ($this->_xml instanceof SimpleXMLElement) {
            if ($value = $this->_xml->xpath($xpath)) {
                return (string) $value[0];
            }
        }
        return null;
    }

    /**
     * Prepare POST data
     *
     * @return string
     */
    protected function _preparePostData()
    {
        $postData = array();
        foreach ($this->getRequest()->getPostData()->getData() as $k => $v) {
            $postData[] = rawurlencode($k) . '=' . rawurlencode($v);
        }
        return join('&', $postData);
    }

    /**
     * Parse cookie in a response headers
     *
     * @return LoadTest_Url
     */
    protected function _parseResponseCookie()
    {
        $this->getResponse()->setCookie(new LoadTest_Object());
        if ($this->getResponse()->getHeaders()->getSetCookie()) {
            foreach (split(' ', $this->getResponse()->getHeaders()->getSetCookie()) as $cookieString) {
                $cookieData = split('=', chop($cookieString, ';'));
                if ($cookieData[0] == 'path') {
                    continue;
                }
                $this->getResponse()->getCookie()->setdata(rawurldecode($cookieData[0]), rawurldecode($cookieData[1]));
            }
        }

        return $this;
    }

    /**
     * Clear request and response data
     * skip xml object
     *
     * @return LoadTest_Url
     */
    public function clear()
    {
        $request = array(
            'host'          => null,
            'path'          => '/',
            'method'        => 'GET',
            'port'          => 80,
            'timeout'       => 30,
            'get_data'      => $this->getRequest()->getGetData()->unsData(),
            'post_data'     => $this->getRequest()->getPostData()->unsData(),
            'cookie_data'   => $this->getRequest()->getCookieData()->unsData()
        );
        $this->getRequest()->setData($request);
        $this->getResponse()->unsData();

        return $this;
    }

    /**
     * Fetch
     *
     * @return LoadTest_Url
     */
    public function fetch()
    {
        /** check variables */
        if (!$this->getRequest()->getHost()) {
            throw new Exception('Invalid hostname');
        }

        $errorNumber = $errorString = null;
        try {
            $this->_handler = fsockopen($this->getRequest()->getHost(), $this->getRequest()->getPort(), $errorNumber, $errorString, $this->getRequest()->getTimeout());
        }
        catch (Exception $e) {
            throw new Exception(sprintf('%s, %s', $errorNumber, $errorString));
        }

        /** prepare request headers */
        $request = array();
        $request[] = $this->getRequest()->getMethod() . ' '.$this->getRequest()->getPath(). ' HTTP/1.1';
        $request[] = 'Host: ' . $this->getRequest()->getHost();
        $request[] = 'User-Agent: LoadTest spider';
        if ($this->getRequest()->getMethod() == 'POST') {
            $postData = $this->_preparePostData();
            $request[] = 'Content-Length: ' . strlen($postData);
        }
        $request[] = 'Connection: Close';
        $request = join("\r\n", $request) . "\r\n\r\n";
        if ($this->getRequest()->getMethod() == 'POST') {
            $request .= $postData;
        }

        fwrite($this->_handler, $request);

        $isBody = false;
        $content = '';
        $this->getResponse()
            ->setHttpCode(0)
            ->setHttpDescription(null)
            ->setHeaders(new LoadTest_Object());
        $this->getResponse()->getHeaders()->setContentType();

        while (!feof($this->_handler)) {
            $str = fgets($this->_handler);
            /** headers */
            if (!$isBody) {
                if ($str == "\r\n") {
                    $isBody = true;
                }
                else {
                    if (preg_match('/HTTP\/\d\.\d (\d+) (.*)/', trim($str), $match)) {
                        $this->getResponse()->setHttpCode($match[1]);
                        $this->getResponse()->setHttpDescription($match[2]);
                    }
                    elseif (preg_match('/([a-zA-Z-]+)\: (.*)/', trim($str), $match)) {
                        $key = strtolower(str_replace('-', '_', $match[1]));
                        $this->getResponse()->getHeaders()->setData($key, $match[2]);
                    }
                }
            }
            else {
                $content .= $str;
            }
        }

        $this->getResponse()->setContent($content);
        $this->_parseResponseCookie();

        if ($this->getResponse()->getHeaders()->getContentType() == 'text/xml') {
            try {
                $this->_xml = new SimpleXMLElement($this->getResponse()->getContent());
                $this->getResponse()->setXml($this->_xml);
            }
            catch (Exception $e) {
                throw new Exception(sprintf("%s\n\n%s", $e->getMessage(), $this->getResponse()->getContent()));
            }
        }

        return $this;
    }
}

class LoadTest_Spider
{
    protected $_usage;

    protected $_args;

    protected $_urls = array();

    protected $_isAuth;

    protected $_xml;

    protected $_cookie = array();

    protected $_url;

    public function __construct()
    {
        $this->_usage = '
#USAGE:
-------------------------------------------------------------------------------
$> php -f Spider.php -- --key keyString --file /path/to/file
-------------------------------------------------------------------------------

# Input file format:
-------------------------------------------------------------------------------
WARNING: first url must be http://yourdomain.com/path/to/loadtest/
-------------------------------------------------------------------------------
http://yourdomain.com/loadtest/
http://yourdomain.com/yyy/ POST:"first_param=value;second_param=second_value;"
http://yourdomain.com/yyy/ POST:"first_param=\nstring with\;, \', \= and \" symbol!"
http://yourdomain.com/yyy/ COOKIE:"param=value;second_param=second_value;"
http://yourdomain.com/yyy/ GET:"..." COOKIE:"..."
http://yourdomain.com/zzz/{{response.node_id}}/
-------------------------------------------------------------------------------

# Input format description
-------------------------------------------------------------------------------
METHODS:
    ---------------------------------------------------------------------------
    USING: by way of space METOD:"param=value;anotherParam=value"
    ---------------------------------------------------------------------------
    POST        Send post query with params
    GET         Generate Zend Compatible Get query with params
    COOKIE      Add params to cookie

REPLACEMENT PARAMS:
    {{parent_node.child_node}}   convert to value previous XML path
                                 //root/parent_node/child_node
-------------------------------------------------------------------------------

#ERROR:
    ';
        $this->_checkArgs();
        $this->_process();
    }

    protected function _getArgs()
    {
        if (is_null($this->_args)) {
            $this->_args = array();
            $argCurrent = null;
            foreach ($_SERVER['argv'] as $arg) {
                if (preg_match('/^--(.*)$/', $arg, $match)) {
                    $argCurrent = $match[1];
                    $this->_args[$argCurrent] = true;
                }
                else {
                    if ($argCurrent) {
                        $this->_args[$argCurrent] = $arg;
                    }
                }
            }
        }
    }

    protected function _checkArgs()
    {
        $this->_getArgs();

        if (!isset($this->_args['key'])) {
            throw new Exception(sprintf("%sKey is required\n", $this->_usage));
        }
        if (!isset($this->_args['file']) || !file_exists($this->_args['file'])) {
            throw new Exception(sprintf("%sFile '%s' is not exists\n", $this->_usage, $this->_args['file']));
        }
        if (version_compare(phpversion(), '5.2.0', '<') === true) {
            throw new Exception(sprintf("%sWhoops, it looks like you have an invalid PHP version.\n    Magento supports PHP 5.2.0 or newer!\n", $this->_usage));
        }
    }

    protected function _process()
    {
        $this->_urls = file($this->_args['file']);
        reset($this->_urls);

        while (list($k, $v) = each($this->_urls)) {
            $str = trim($v);
            if (empty($str)) {
                continue;
            }
            $this->_fetchUrl($str);
        }
    }

    protected function _fetchUrl($url)
    {
        if (is_null($this->_url)) {
            $this->_url = new LoadTest_Url();
        }
        $this->_url->clear();
        $this->_url->parse($url);
        if (!$this->_isAuth) {
            if (!preg_match('/\/loadtest\/$/', $this->_url->getRequest()->getPath())) {
                throw new Exception(sprintf('%sIncorrect first url! Current path "%s"', $this->_usage, $this->_url->getRequest()->getPath()));
            }
            $this->_url->getRequest()->setPath($this->_url->getRequest()->getPath() . 'index/spider/');
            try {
                $this->_url->fetch();
            }
            catch (Exception $e) {
                throw new Exception(sprintf('%s%s', $this->_usage, $e->getMessage()));
            }

            if ($this->_url->getResponse()->getHeaders()->getContentType() != 'text/xml') {
                throw new Exception(sprintf('%sInvalid Load Performance Testing page content', $this->_usage));
            }

            $xml = $this->_url->getResponse()->getXml();
            /* @var $xml SimpleXMLElement */
            if (!(int)$xml->status) {
                throw new Exception(sprintf('%sLoad Performance Testing is disable', $this->_usage));
            }
            if (!(int)$xml->logged_in) {
                throw new Exception(sprintf('%sAccess denied, access key isn\'t valid', $this->_usage));
            }

            $this->_isAuth = true;
            $this->_cookie = $this->_url->getResponse()->getCookie()->getData();
        }
        else {
            $this->_url->getRequest()->getCookieData()->addData($this->_cookie);

            try {
                $this->_url->fetch();
            }
            catch (Exception $e) {
                throw new Exception(sprintf('%s%s', $this->_usage, $e->getMessage()));
            }

            if ($this->_url->getResponse()->getHeaders()->getContentType() != 'text/xml') {
                throw new Exception(sprintf('%sInvalid Load Performance Testing page content', $this->_usage));
            }

            if ($this->_url->getResponse()->getXml()->response->fetch_urls) {

            }

            print $this->_url->getResponse()->getContent() . "\n";
        }
    }

//    protected function _fetchUrl($urlData)
//    {
//        if (!is_array($urlData) || empty($urlData['host']) || empty($urlData['path'])) {
//            return false;
//        }
//
//        $errNo = $errStr = null;
//        try {
//            $fp = fsockopen($urlData['host'], 80, $errNo, $errStr);
//        }
//        catch (Exception $e) {
//            throw new Exception(sprintf("%s#%s: %s\n",
//                $this->_usage,
//                $errNo,
//                $errStr
//            ));
//        }
//
//        $method = 'GET';
//        $postData = '';
//
//        $request = array();
//        if (!empty($urlData['methods']['POST'])) {
//            $method = 'POST';
//            foreach ($urlData['methods']['POST'] as $k => $v) {
//                $postData .= rawurlencode($k).'='.rawurlencode($v).'&';
//            }
//        }
//
//        $request[] = $method . ' ' . $urlData['path'] . ' HTTP/1.1';
//        $request[] = 'Host: ' . $urlData['host'];
//        $request[] = 'User-Agent: LoadTest Spider v' . $this->_version;
//
//        /**
//         * Cookie
//         */
//        $cookieData = '';
//        foreach ($this->_cookie as $k => $v) {
//            $cookieData .= rawurlencode($k) . '=' . rawurlencode($v) . '; ';
//        }
//        if (!empty($urlData['methods']['COOKIE'])) {
//            foreach ($urlData['methods']['COOKIE'] as $k => $v) {
//                $cookieData .= rawurlencode($k) . '=' . rawurlencode($v) . '; ';
//            }
//        }
//        if ($cookieData) {
//            $request[] = 'Cookie: ' . $cookieData;
//        }
//        if ($method == 'POST') {
//            $request[] = 'Content-Length: ' . strlen($postData);
//        }
//        $request[] = 'Connection: Close';
//
//        if ($method == 'POST') {
//            $request = join("\r\n", $request) . "\r\n\r\n" . $postData;
//        }
//        else {
//            $request = join("\r\n", $request) . "\r\n\r\n";
//        }
//
//        fwrite($fp, $request);
//
//        $isBody         = false;
//        $response       = array();
//        $responseCode   = 0;
//        $responseStatus = 0;
//        $content        = '';
//        $contentType    = false;
//
//        while (!feof($fp)) {
//            $str = fgets($fp);
//            if (!$isBody) {
//                if ($str == "\r\n") {
//                    $isBody = true;
//                }
//                else {
//                    if (preg_match('/HTTP\/\d.\d (\d+) (.*)/', trim($str), $match)) {
//                        $responseCode = $match[1];
//                        $responseStatus = $match[2];
//                    }
//                    elseif (preg_match('/([a-zA-Z-]+)\: (.*)/', trim($str), $match)) {
//                        $response[strtolower($match[1])] = $match[2];
//                    }
//                }
//            }
//            else {
//                $content .= $str;
//            }
//        }
//
//        return array(
//            'response_headers'  => $response,
//            'response_code'     => $responseCode,
//            'response_status'   => $responseStatus,
//            'content_type'      => !empty($response['content-type']) ? $response['content-type'] : null,
//            'content'           => $content
//        );
//    }
//
//    protected function _fetchUrl($urlData)
//    {
//        if (!$this->_isAuth) {
//            if (!preg_match('/\/loadtest\/$/', $urlData['path'])) {
//                throw new Exception(sprintf("%sIncorrect first url! %s%s\n", $this->_usage, $urlData['host'], $urlData['path']));
//            }
//            $errno = $errstr = null;
//            $fp = fsockopen($urlData['host'], 80, $errno, $errstr);
//            if ($fp === false) {
//                throw new Exception(sprintf("%s%s: %s\n", $this->_usage, $errno, $errstr));
//            }
//
//            $request = array();
//            $request[] = "GET ".$urlData['path']."index/spider/ HTTP/1.1";
//            $request[] = "Host: ".$urlData['host'];
//            $request[] = "User-Agent: LoadTest spider";
//            $request[] = "Connection: Close";
//
//            fwrite($fp, join("\r\n", $request) . "\r\n\r\n");
//
//            $isBody         = false;
//            $response       = array();
//            $responseCode   = null;
//            $content        = null;
//            $contentType    = false;
//
//            while (!feof($fp)) {
//                $str = fgets($fp);
//                if (!$isBody) {
//                    if ($str == "\r\n") {
//                        $isBody = true;
//                    }
//                    else {
//                        if (preg_match('/HTTP\/\d.\d (\d+) (.*)/', trim($str), $match)) {
//                            $responseCode = $match[1];
//                            $responseStatus = $match[2];
//                        }
//                        elseif (preg_match('/([a-zA-Z-]+)\: (.*)/', trim($str), $match)) {
//                            $response[$match[1]] = $match[2];
//                        }
//                    }
//                }
//                else {
//                    $content .= $str;
//                }
//            }
//
//            if ($responseCode != 200) {
//                throw new Exception(sprintf("%sInvalid Load Performance Testing page status '%d %s'\n",
//                    $this->_usage,
//                    $responseCode,
//                    $responseStatus
//                ));
//            }
//
//            if (!empty($response['Content-Type'])) {
//                $strpos = strpos($response['Content-Type'], ';');
//                if ($strpos) {
//                    $contentType = substr($response['Content-Type'], 0, $strpos);
//                }
//                else {
//                    $contentType = $response['Content-Type'];
//                }
//            }
//
//            if (!$contentType || $contentType != 'text/xml') {
//                throw new Exception(sprintf("%sInvalid Load Performance Testing page content type\n", $this->_usage));
//            }
//
//            try {
//                $this->_xml = new SimpleXMLElement($content);
//            }
//            catch (Exception $e) {
//                throw new Exception(sprintf("%sInvalid Load Performance Testing responce\n    %s\n",
//                    $this->_usage,
//                    $e->getMessage()
//                ));
//            }
//
//            $this->_isAuth = (int)$this->_xml->logged_in;
//            $this->_isEnable = (int)$this->_xml->status;
//
//            if (!$this->_isEnable) {
//                throw new Exception(sprintf("%sLoad Performance Testing on '%s' is disable\n",
//                    $this->_usage,
//                    $urlData['host']
//                ));
//            }
//            if (!$this->_isAuth) {
//                throw new Exception(sprintf("%sInvalid authorization key\n",
//                    $this->_usage
//                ));
//            }
//
//            foreach (split(' ', $response['Set-Cookie']) as $v) {
//                print substr($v,0,strpos($v,';'))."\n\n";
//            }
//
//            var_dump($response);
//        }
//        else {
//
//        }
//    }
}

try {
    $spider = new LoadTest_Spider();
}
catch (Exception $e) {
    die($e->getMessage());
}