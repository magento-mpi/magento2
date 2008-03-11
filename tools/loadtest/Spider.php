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
                $query = array();
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
            $methods = $params = array();
            preg_match_all('/([A-Z]+)\:([\'|\\\"])(.*?[^\\\\])\\2.*?/', $data, $methods, PREG_SET_ORDER);

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
                $match = array();

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
     * Prepare Cookie data
     *
     * @return string
     */
    protected function _prepareCookieData()
    {
        $cookieData = array();
        foreach ($this->getRequest()->getCookieData()->getData() as $k => $v) {
            $cookieData[] = rawurlencode($k) . '=' . rawurlencode($v);
        }
        return join('; ', $cookieData);
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
            if (!is_array($this->getResponse()->getHeaders()->getSetCookie())) {
                $cookies = array($this->getResponse()->getHeaders()->getSetCookie());
            }
            else {
                $cookies = $this->getResponse()->getHeaders()->getSetCookie();
            }
            foreach ($cookies as $cookie) {
                foreach (split('; ', $cookie) as $cookieString) {
                    if (empty($cookieString)) {
                        continue;
                    }
                    $cookieData = split('=', trim($cookieString, ';'));
                    if ($cookieData[0] == 'path') {
                        continue;
                    }
                    if ($cookieData[0] == 'expires') {
                        continue;
                    }
                    if ($cookieData[0] == 'domain') {
                        continue;
                    }
                    $this->getResponse()->getCookie()->setData(
                         rawurldecode(isset($cookieData[0]) ? $cookieData[0] : ''),
                         rawurldecode(isset($cookieData[1]) ? $cookieData[1] : '')
                    );
                }
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
            $_handler = fsockopen($this->getRequest()->getHost(), $this->getRequest()->getPort(), $errorNumber, $errorString, $this->getRequest()->getTimeout());
        }
        catch (Exception $e) {
            throw new Exception(sprintf('%s, %s', $errorNumber, $errorString));
        }

        $path = $this->getRequest()->getPath();
        foreach($this->getRequest()->getGetData()->getData() as $k => $v) {
            $path .= rawurlencode($k) . '/' . rawurlencode($v) . '/';
        }

        /** prepare request headers */
        $request = array();
        $request[] = $this->getRequest()->getMethod() . ' ' . $path . ' HTTP/1.0';
        $request[] = 'Host: ' . $this->getRequest()->getHost();
        $request[] = 'User-Agent: LoadTest spider';
        if ($this->getRequest()->getCookieData()->getData()) {
            $cookieData = $this->_prepareCookieData();
            $request[] = 'Cookie: ' . $cookieData;
        }
        if ($this->getRequest()->getMethod() == 'POST') {
            $postData = $this->_preparePostData();
            $request[] = 'Content-Length: ' . strlen($postData);
        }
        $request[] = 'Connection: Close';
        $request = join("\r\n", $request) . "\r\n\r\n";
        if ($this->getRequest()->getMethod() == 'POST') {
            $request .= $postData;
        }

        $this->getRequest()->setOriginal($request);

        fwrite($_handler, $request);

        $isBody = false;
        $content = '';
        $this->getResponse()
            ->setHttpCode(0)
            ->setHttpDescription(null)
            ->setHeaders(new LoadTest_Object());
        $this->getResponse()->getHeaders()->setContentType();

        $response = null;

        while (!feof($_handler)) {
            $str = fgets($_handler);
            /** headers */
            if (!$isBody) {
                if ($str == "\r\n") {
                    $isBody = true;
                }
                else {
                    $response .= $str;
                    $match = array();
                    if (preg_match('/HTTP\/\d\.\d (\d+) (.*)/', trim($str), $match)) {
                        $this->getResponse()->setHttpCode($match[1]);
                        $this->getResponse()->setHttpDescription($match[2]);
                    }
                    elseif (preg_match('/([a-zA-Z-]+)\: (.*)/', trim($str), $match)) {
                        $key = strtolower(str_replace('-', '_', $match[1]));
                        if (is_null($this->getResponse()->getHeaders()->getData($key))) {
                            $this->getResponse()->getHeaders()->setData($key, $match[2]);
                        }
                        else {
                            if (is_array($this->getResponse()->getHeaders()->getData($key))) {
                                $data = $this->getResponse()->getHeaders()->getData($key);
                                $data[] = $match[2];
                                $this->getResponse()->getHeaders()->setData($key, $data);
                            }
                            else {
                                $data = array($this->getResponse()->getHeaders()->getData($key), $match[2]);
                                $this->getResponse()->getHeaders()->setData($key, $data);
                            }
                        }
                    }
                }
            }
            else {
                $content .= $str;
            }
        }

        fclose($_handler);

        $this->getResponse()->setOriginal($response);
        $this->getResponse()->setContent($content);

        $this->_parseResponseCookie();

        if ($this->getResponse()->getHeaders()->getContentType() == 'text/xml') {
            try {
                $this->_xml = new SimpleXMLElement($this->getResponse()->getContent());
                $this->getResponse()->setXml($this->_xml);
            }
            catch (Exception $e) {
                $this->_throwException("");
                throw new Exception($e->getMessage());
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

    /**
     * Url model
     *
     * @var Loadtest_Url
     */
    protected $_url;

    public function __construct()
    {
        $this->_usage = '

-------------------------------------------------------------------------------
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
                $match = array();
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

        while (list(, $v) = each($this->_urls)) {
            $str = trim($v);
            if (empty($str)) {
                continue;
            }
            elseif (substr($str, -1) == '#' || substr($str, -2) == '//') {
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
                $this->_throwException('%sIncorrect first url! Current path "%s"', array($this->_usage, $this->_url->getRequest()->getPath()));
            }
            $this->_url->getRequest()->setPath($this->_url->getRequest()->getPath() . 'index/spider/');
            $this->_url->getRequest()->getGetData()->setKey($this->_args['key']);

            try {
                $this->_url->fetch();
            }
            catch (Exception $e) {
                $this->_throwException('%s%s', array($this->_usage, $e->getMessage()));
            }

            if ($this->_url->getResponse()->getHeaders()->getContentType() != 'text/xml') {
                $this->_throwException('%sInvalid Load Performance Testing page content', array($this->_usage));
            }

            $xml = $this->_url->getResponse()->getXml();
            /* @var $xml SimpleXMLElement */
            if (!(int)$xml->status) {
                $this->_throwException('%sLoad Performance Testing is disable', array($this->_usage));
            }

            if (!(int)$xml->logged_in) {
                $this->_throwException('%sAccess denied, access key isn\'t valid', array($this->_usage));
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
                $this->_throwException('%s%s', array($this->_usage, $e->getMessage()));
            }

            if ($this->_url->getResponse()->getHeaders()->getContentType() != 'text/xml') {
                $this->_throwException('%sInvalid Load Performance Testing page content', array($this->_usage));
            }

            if ($this->_url->getResponse()->getXml()->response->fetch_urls) {
                $urls = array();
                foreach ($this->_url->getResponse()->getXml()->response->fetch_urls->children() as $url) {
                    $urls[] = (string)$url;
                }
                if ($key = key($this->_urls)) {
                    $this->_urls = array_merge($urls, array_slice($this->_urls, $key));
                }
                else {
                    $this->_urls = $urls;
                }
                
                reset($this->_urls);
            }

            print str_replace("\r", '', str_replace("\n", '', $this->_url->getResponse()->getContent())) . "\n";
        }
    }

    protected function _throwException($errorMsg, $args = array())
    {
        $args[] = "\n\n" . str_repeat('-', 35) . ' REQUEST ' . str_repeat('-', 35) . "\n"
            . $this->_url->getRequest()->getOriginal() . str_repeat('-', 80) . "\n"
            . str_repeat('-', 35) . ' RESPONSE ' . str_repeat('-', 34) . "\n"
            . $this->_url->getResponse()->getOriginal() . str_repeat('-', 80) . "\n"
            . str_repeat('-', 35) . ' CONTENT ' . str_repeat('-', 35) . "\n"
            . $this->_url->getResponse()->getContent() . str_repeat('-', 80) . "\n\n";

        throw new Exception(vsprintf($errorMsg . '%s', $args));
    }
}

try {
    $spider = new LoadTest_Spider();
}
catch (Exception $e) {
    die($e->getMessage());
}