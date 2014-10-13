<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Client;

/**
 * Solr client
 */
class Solr extends \Apache_Solr_Service
{
    /**
     * Store user login, that needed in authentication with solr server
     *
     * @var string
     */
    protected $_login = '';

    /**
     * Store user password, that needed in authentication with solr server
     *
     * @var string
     */
    protected $_password = '';

    /**
     * Suggestions servlet
     */
    const SUGGESTIONS_SERVLET = 'spell';

    /**
     * Constructed servlet full path URLs
     *
     * @var string
     */
    protected $_suggestionsUrl;

    /**
     * Initialize Solr Client
     *
     * @param array $options
     * @throws \Magento\Framework\Model\Exception
     */
    public function __construct($options = array())
    {
        $_optionsNames = array('hostname', 'login', 'password', 'port', 'path');
        if (!sizeof(array_intersect($_optionsNames, array_keys($options)))) {
            throw new \Magento\Framework\Model\Exception(
                __('We were unable to perform the search because a search engine misconfiguration.')
            );
        }

        $this->setUserLogin($options['login']);
        $this->setPassword($options['password']);

        if (isset($options['timeout'])) {
            ini_set('default_socket_timeout', $options['timeout']);
        }

        parent::__construct($options['hostname'], $options['port'], '/' . $options['path'] . '/');
        return $this;
    }

    /**
     * Initialize urls
     *
     * @return void
     */
    protected function _initUrls()
    {
        parent::_initUrls();
        $this->_suggestionsUrl = $this->_constructUrl(self::SUGGESTIONS_SERVLET);
    }

    /**
     * Send an rollback command.
     *
     * @param float|int $timeout Maximum expected duration of the commit operation on the server (otherwise, will throw a communication exception)
     * @return \Apache_Solr_Response
     *
     * @throws \Exception If an error occurs during the service call
     */
    public function rollback($timeout = 3600)
    {
        $rawPost = '<rollback/>';
        return $this->_sendRawPost($this->_updateUrl, $rawPost, $timeout);
    }

    /**
     * Create a delete document based on a multiple queries and submit it
     *
     * @param array $rawQueries Expected to be utf-8 encoded
     * @param bool $fromPending
     * @param bool $fromCommitted
     * @param float|int $timeout Maximum expected duration of the delete operation on the server (otherwise, will throw a communication exception)
     * @return \Apache_Solr_Response
     *
     * @throws \Exception If an error occurs during the service call
     */
    public function deleteByQueries($rawQueries, $fromPending = true, $fromCommitted = true, $timeout = 3600)
    {
        $pendingValue = $fromPending ? 'true' : 'false';
        $committedValue = $fromCommitted ? 'true' : 'false';

        $rawPost = '<delete fromPending="' . $pendingValue . '" fromCommitted="' . $committedValue . '">';

        foreach ($rawQueries as $query) {
            //escape special xml characters
            $query = htmlspecialchars($query, ENT_NOQUOTES);

            $rawPost .= '<query>' . $query . '</query>';
        }

        $rawPost .= '</delete>';

        return $this->delete($rawPost, $timeout);
    }

    /**
     * Alias to \Apache_Solr_Service::deleteByMultipleIds() method
     *
     * @param array $ids Expected to be utf-8 encoded strings
     * @param bool $fromPending
     * @param bool $fromCommitted
     * @param float|int $timeout Maximum expected duration of the delete operation on the server (otherwise, will throw a communication exception)
     * @return void
     * @throws \Exception If an error occurs during the service call
     */
    public function deleteByIds($ids, $fromPending = true, $fromCommitted = true, $timeout = 3600)
    {
        $this->deleteByMultipleIds($ids, $fromPending, $fromCommitted, $timeout);
    }

    /**
     * Central method for making a get operation against this Solr Server
     *
     * @param string $url
     * @param bool|float $timeout Read timeout in seconds
     * @return \Apache_Solr_Response
     */
    protected function _sendRawGet($url, $timeout = false)
    {
        $this->_setAuthHeader($this->_getContext);
        \Magento\Framework\Profiler::start(
            'solr_send_raw_get',
            array('group' => 'solr', 'operation' => 'solr:_sendRawGet', 'host' => $this->getHost())
        );
        $response = parent::_sendRawGet($url, $timeout);
        \Magento\Framework\Profiler::stop('solr_send_raw_get');
        return $response;
    }

    /**
     * Central method for making a post operation against this Solr Server
     *
     * @param string $url
     * @param string $rawPost
     * @param bool|float $timeout Read timeout in seconds
     * @param string $contentType
     * @return \Apache_Solr_Response
     */
    protected function _sendRawPost($url, $rawPost, $timeout = false, $contentType = 'text/xml; charset=UTF-8')
    {
        $this->_setAuthHeader($this->_postContext);
        \Magento\Framework\Profiler::start(
            'solr_send_raw_post',
            array('group' => 'solr', 'operation' => 'solr:_sendRawPost', 'host' => $this->getHost())
        );
        $response = parent::_sendRawPost($url, $rawPost, $timeout, $contentType);
        \Magento\Framework\Profiler::stop('solr_send_raw_post');
        return $response;
    }

    /**
     * Set Authorization header to stream context.
     *
     * @param resource $context
     * @return void
     */
    protected function _setAuthHeader($context)
    {
        stream_context_set_option(
            $context,
            'http',
            'header',
            'Authorization: Basic ' . base64_encode($this->getUserLogin() . ':' . $this->getPassword())
        );
    }

    /**
     * Setter for solr server username
     *
     * @param string $username
     * @return void
     */
    public function setUserLogin($username)
    {
        $this->_login = (string)$username;
    }

    /**
     * Getter of solr server username
     *
     * @return string
     */
    public function getUserLogin()
    {
        return $this->_login;
    }

    /**
     * Setter for solr server password
     *
     * @param string $username
     * @return void
     */
    public function setPassword($username)
    {
        $this->_password = (string)$username;
    }

    /**
     * Getter of solr server password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }
}
