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
     * Visitor info data table
     *
     * @var string
     */
    protected $_visitorInfoTable;

    /**
     * Customer data table
     *
     * @var string
     */
    protected $_customerTable;

    /**
     * Url info data table
     *
     * @var string
     */
    protected $_urlInfoTable;

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

    protected $_visitorId;

    public function __construct()
    {
        $this->_visitorTable = Mage::getSingleton('core/resource')->getTableName('log/visitor');
        $this->_visitorInfoTable = Mage::getSingleton('core/resource')->getTableName('log/visitor_info');

        $this->_urlTable = Mage::getSingleton('core/resource')->getTableName('log/url_table');
        $this->_urlInfoTable = Mage::getSingleton('core/resource')->getTableName('log/url_info_table');

        $this->_customerTable = Mage::getSingleton('core/resource')->getTableName('log/customer');

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

    public function logVisitor(Mage_Log_Model_Visitor $collectedData)
    {
        $sessId = $collectedData->getSessionId();
		if ($this->_write) {
		    $data = array(
		              'session_id' => $sessId,
		              'first_visit_at' => $collectedData->getFirstVisitAt(),
		              'last_visit_at' => $collectedData->getLastVisitAt(),
		              'last_url_id' => intval($collectedData->getLastUrlId())
		          );

	        $exists = $this->_write->fetchOne("SELECT session_id FROM $this->_visitorTable WHERE session_id = ?", array($sessId));
	        if ($exists) {
	            $where = $this->_write->quoteInto('session_id=?', $sessId);
	            $this->_write->update($this->_visitorTable, $data, $where);
	        } else {
	            $this->_write->insert($this->_visitorTable, $data);

	            $visitorId = $this->_write->lastInsertId();

	            $data = array(
	                   'visitor_id' => $visitorId,
	                   'http_referer' => $collectedData->getHttpReferer(),
	                   'http_user_agent' => $collectedData->getHttpUserAgent(),
	                   'http_accept_charset' => $collectedData->getHttpAcceptCharset(),
	                   'http_accept_language' => $collectedData->getHttpAcceptLanguage(),
	                   'server_addr' => $collectedData->getServerAddr(),
	                   'remote_addr' => $collectedData->getRemoteAddr()
	               );
                $this->_write->insert($this->_visitorInfoTable, $data);
	        }
		}

		return $this;
    }

    public function logCustomer(Mage_Log_Model_Visitor $collectedData)
    {
        $sessId = $collectedData->getSessionId();
        if ($this->_write && $collectedData->getCustomerId() > 0 ) {
		    $data = array(
		          'visitor_id' => $this->getVisitorId($sessId),
		          'customer_id' => $collectedData->getCustomerId(),
		          'login_at' => $collectedData->getLoginAt(),
		          'logout_at' => $collectedData->getLogoutAt()
		      );

		    foreach ($data as $key => $value) {
		    	if( !$value ) {
		    		unset($data[$key]);
		    	}
		    }

	        if( $collectedData->getLogoutAt() ) {
	            $where = $this->_write->quoteInto('log_id=?', $this->getLogId( $collectedData->getCustomerId(), $this->getVisitorId($sessId) ));
	            $this->_write->update($this->_customerTable, $data, $where);
	        } elseif( $collectedData->getLoginAt() ) {
	            $this->_write->insert($this->_customerTable, $data);
	        }
		}

		return $this;
    }

    public function logUrl(Mage_Log_Model_Visitor $collectedData)
    {
		if ($this->_write) {
		    $sessId = $collectedData->getSessionId();
            $data = array(
                        'visit_time' => $this->getNow(),
                        'visitor_id' => $this->getVisitorId($sessId)
                    );
            $this->_write->insert($this->_urlTable, $data);

            $urlId = $this->_write->lastInsertId();

            $where = $this->_write->quoteInto("{$this->_visitorTable}.session_id = ?", $collectedData->getSessionId());
            $this->_write->update($this->_visitorTable, array('last_url_id' => $urlId), $where);

            $data = array(
                        'url_id' => $urlId,
                        'url' => $collectedData->getUrl(),
                        'referer' => $collectedData->getHttpReferer()
                    );
            $this->_write->insert($this->_urlInfoTable, $data);
		}

		return $this;
    }

    public function saveUrl(Mage_Log_Model_Visitor $visitor)
    {
        $data = array(
                    'session_id' => $visitor->getSessionId(),
                    'url_value' => $visitor->getUrl(),
                    'visit_time' => $this->getNow()
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

    public function getVisitorId($sessionId)
    {
        if( intval($this->_visitorId) <= 0 ) {
            $this->_visitorId = $this->_write->fetchOne("SELECT visitor_id FROM $this->_visitorTable WHERE session_id = ?", array($sessionId));
        }
        return $this->_visitorId;
    }

    public function getLogId($customerId, $visitorId)
    {
        return $this->_read->fetchOne("SELECT MAX(log_id) FROM {$this->_customerTable} WHERE {$this->_customerTable}.customer_id = ? AND {$this->_customerTable}.visitor_id = ? ", array($customerId, $visitorId));
    }
}