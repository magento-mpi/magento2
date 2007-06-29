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
     * Log quote data table name.
     *
     * @var string
     */
    protected $_quoteTable;

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
        $resource = Mage::getSingleton('core/resource');

        $this->_visitorTable = $resource->getTableName('log/visitor');
        $this->_visitorInfoTable = $resource->getTableName('log/visitor_info');

        $this->_urlTable = $resource->getTableName('log/url_table');
        $this->_urlInfoTable = $resource->getTableName('log/url_info_table');

        $this->_customerTable = $resource->getTableName('log/customer');
        $this->_quoteTable = $resource->getTableName('log/quote_table');

        $this->_read = $resource->getConnection('log_read');
        $this->_write = $resource->getConnection('log_write');
    }

    public function load($visitorId)
    {
    	$data = array();
    	if ($this->_read) {
            try {
                $data = $this->_read->fetchRow("SELECT * FROM $this->_visitorTable WHERE visitor_id = ?", array($visitorId));
            } catch (Exception $e) {
                Mage::log($e);
            }
    	}
        return $data;
    }

    public function logVisitor(Mage_Log_Model_Visitor $collectedData)
    {
        $sessId = $collectedData->getSessionId();
		if ($this->_write) {
		    $this->_write->beginTransaction();
		    try {
    		    $data = array(
    		              'session_id' => $sessId,
    		              'first_visit_at' => $collectedData->getFirstVisitAt(),
    		              'last_visit_at' => $collectedData->getLastVisitAt(),
    		              'last_url_id' => intval($collectedData->getLastUrlId())
    		          );

    	        if ($collectedData->getVisitorId()) {
        		    if( $collectedData->getLogoutNeeded() ) {
        		        $visitorId = $collectedData->getVisitorId();
        	            $query = $this->_write->quoteInto("UPDATE {$this->_customerTable} SET logout_at = ? WHERE visitor_id = {$visitorId}", new Zend_Db_Expr('NOW()') );
        		        $this->_write->query($query);
        		    }

    	            $where = $this->_write->quoteInto('visitor_id=?', $collectedData->getVisitorId());
    	            $this->_write->update($this->_visitorTable, $data, $where);
    	        } else {
    	            $this->_write->insert($this->_visitorTable, $data);
    	            $collectedData->setVisitorId($this->_write->lastInsertId());

    	            $data = array(
    	                   'visitor_id' => $collectedData->getVisitorId(),
    	                   'http_referer' => $collectedData->getHttpReferer(),
    	                   'http_user_agent' => $collectedData->getHttpUserAgent(),
    	                   'http_accept_charset' => $collectedData->getHttpAcceptCharset(),
    	                   'http_accept_language' => $collectedData->getHttpAcceptLanguage(),
    	                   'server_addr' => $collectedData->getServerAddr(),
    	                   'remote_addr' => $collectedData->getRemoteAddr()
    	               );
                    $this->_write->insert($this->_visitorInfoTable, $data);
    	        }
                $this->_write->commit();
	        } catch (Exception $e) {
	            $this->_write->rollBack();
	            Mage::log($e);
	        }
		}
		return $this;
    }

    public function logCustomer(Mage_Log_Model_Visitor $collectedData)
    {
        if ($this->_write && ($collectedData->getCustomerId() > 0)  && $collectedData->getVisitorId() ) {
		    $data = array(
		          'visitor_id' => $collectedData->getVisitorId(),
		          'customer_id' => $collectedData->getCustomerId(),
		          'login_at' => $collectedData->getLoginAt(),
		          'logout_at' => $collectedData->getLogoutAt()
		      );

		    foreach ($data as $key => $value) {
		    	if( !$value ) {
		    		unset($data[$key]);
		    	}
		    }

		    $this->_write->beginTransaction();
            try {
                if( $collectedData->getLogoutAt() ) {
                    $where = $this->_write->quoteInto('log_id=?', $this->getLogId( $collectedData->getCustomerId(), $collectedData->getVisitorId() ));
                    $this->_write->update($this->_customerTable, $data, $where);
                } elseif( $collectedData->getLoginAt() ) {
                    $this->_write->insert($this->_customerTable, $data);
                }
                $this->_write->commit();
            } catch (Exception $e) {
                $this->_write->rollBack();
                Mage::log($e);
            }
		}
		return $this;
    }

    public function logUrl(Mage_Log_Model_Visitor $collectedData)
    {
		if ($this->_write && $collectedData->getVisitorId()) {
            $data = array(
                        'visit_time' => $this->getNow(),
                        'visitor_id' => $collectedData->getVisitorId()
                    );

            $this->_write->beginTransaction();
            try {
                $this->_write->insert($this->_urlTable, $data);
                $urlId = $this->_write->lastInsertId();
                $where = $this->_write->quoteInto("{$this->_visitorTable}.visitor_id = ?", $collectedData->getVisitorId());
                $this->_write->update($this->_visitorTable, array('last_url_id' => $urlId), $where);

                $data = array(
                            'url_id' => $urlId,
                            'url' => $collectedData->getUrl(),
                            'referer' => $collectedData->getHttpReferer()
                        );
                $this->_write->insert($this->_urlInfoTable, $data);
                $this->_write->commit();
            } catch (Exception $e) {
                $this->_write->rollBack();
                Mage::log($e);
            }
		}

		return $this;
    }

    public function logQuote($collectedData)
    {
        if( $this->_write ) {
            $data = array(
                'quote_id' => $collectedData->getQuoteId(),
                'visitor_id' => $collectedData->getVisitorId(),
                'created_at' => $collectedData->getQuoteCreatedAt(),
                'deleted_at' => $collectedData->getQuoteDeletedAt()
            );

            $this->_write->beginTransaction();
            try {
                $this->_write->insert($this->_quoteTable, $data);
                $this->_write->commit();
            } catch (Exception $e) {
                $this->_write->rollBack();
                Mage::log($e);
            }
        }
        return $this;
    }

    public function getNow()
    {
        return new Zend_Db_Expr('NOW()');
    }

    public function getLogId($customerId, $visitorId)
    {
        return $this->_read->fetchOne("SELECT MAX(log_id) FROM {$this->_customerTable} WHERE {$this->_customerTable}.customer_id = ? AND {$this->_customerTable}.visitor_id = ? ", array($customerId, $visitorId));
    }
 }