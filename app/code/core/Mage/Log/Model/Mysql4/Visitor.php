<?php

class Mage_Log_Model_Mysql4_Visitor
{
    /**
     * Visitor data table name
     *
     * @var string
     */
    protected $_visitorTable;

    /**
     * Log URL data table name.
     *
     * @var string
     */
    protected $_urlTable;

    /**
     * Database read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * Database write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;

    /**
     * Open session
     *
     * @param string $savePath ignored
     * @param string $sessName ignored
     * @return boolean
     */
    public function __construct()
    {
        $this->_visitorTable = Mage::getSingleton('core/resource')->getTableName('log_resource', 'visitor');
        $this->_urlTable = Mage::getSingleton('core/resource')->getTableName('log_resource', 'url_table');

        $this->_read = Mage::getSingleton('core/resource')->getConnection('log_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('log_write');
    }

    public function load($sessId)
    {
    	$data = array();
    	if ($this->_read) {
    		$data = $this->_read->fetchRow("SELECT * FROM $this->_visitorTable WHERE session_id = ?", array($sessId));
    	}
        return $data;
    }

    public function save(Mage_Log_Model_Visitor $visitor)
    {
        $sessId = $visitor->getSessionId();
		if ($this->_write) {
		    $data = array(
		          'session_id' => $visitor->getSessionId(),
		          'first_visit_at' => $visitor->getFirstVisitAt(),
		          'last_visit_at' => $visitor->getLastVisitAt(),
		          'server_addr' => $visitor->getServerAddr(),
		          'remote_addr' => $visitor->getRemoteAddr(),
		          'http_referer' => $visitor->getHttpReferer(),
		          'http_secure' => $visitor->getHttpSecure(),
		          'http_user_agent' => $visitor->getHttpUserAgent(),
		          'http_accept_language' => $visitor->getHttpAcceptLanguage(),
		          'http_accept_charset' => $visitor->getHttpAcceptCharset(),
		          'http_host' => $visitor->getHttpHost(),
		          'website_id' => $visitor->getWebsiteId(),
		          'customer_id' => ( $visitor->getCustomerId() ) ? $visitor->getCustomerId() : 0,
		          'quote_id' => ( $visitor->getQuoteId() > 0 ) ? $visitor->getQuoteId() : 0
		    );

	        $exists = $this->_write->fetchOne("SELECT session_id FROM $this->_visitorTable WHERE session_id = ?", array($sessId));
	        if ($exists) {
	            $where = $this->_write->quoteInto('session_id=?', $sessId);
	            $this->_write->update($this->_visitorTable, $data, $where);
	        } else {
	            $this->_write->insert($this->_visitorTable, $data);
	        }
		}
        return $this;
    }

    public function saveUrl(Mage_Log_Model_Visitor $visitor)
    {
        $data = array(
                    'session_id' => $visitor->getSessionId(),
                    'url_value' => $visitor->getUrl()
                );
        $this->_write->insert($this->_urlTable, $data);

        $where = $this->_write->quoteInto("{$this->_visitorTable}.session_id = ?", $visitor->getSessionId());
        $this->_write->update($this->_visitorTable, array('last_url_id' => $this->_write->lastInsertId()), $where);
        return $this;
    }

    public function delete(Mage_Log_Model_Visitor $visitor)
    {
        $this->_write->query("DELETE FROM $this->_visitorTable WHERE session_id = ?", $visitor->getSessionId());
        return true;
    }

    public function getNow()
    {
        return new Zend_Db_Expr('NOW()');
    }
}