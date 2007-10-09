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
 * @package    Mage_Log
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Visitor log resource
 *
 * @category   Mage
 * @package    Mage_Log
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Log_Model_Mysql4_Visitor extends Mage_Core_Model_Mysql4_Abstract 
{
    protected function _construct()
    {
        $this->_init('log/visitor', 'visitor_id');
    }
    
    /*public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');

        $this->_urlTable = $resource->getTableName('log/url_table');
        $this->_urlInfoTable = $resource->getTableName('log/url_info_table');

        $this->_customerTable = $resource->getTableName('log/customer');
        $this->_quoteTable = $resource->getTableName('log/quote_table');

        $this->_read = $resource->getConnection('log_read');
        $this->_write = $resource->getConnection('log_write');
    }*/
    
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $visitor)
    {
        return array(
            'session_id'    => $visitor->getSessionId(),
            'first_visit_at'=> $visitor->getFirstVisitAt(),
            'last_visit_at' => $visitor->getLastVisitAt(),
            'last_url_id'   => $visitor->getLastUrlId() ? $visitor->getLastUrlId() : 0,
        );
    }
    
    /**
     * Saving information about url
     *
     * @param   Mage_Log_Model_Visitor $visitor
     * @return  Mage_Log_Model_Mysql4_Visitor
     */
    protected function _saveUrlInfo($visitor)
    {
        $write = $this->getConnection('write');
        $write->insert($this->getTable('log/url_info_table'), array(
            'url'    => $visitor->getUrl(),
            'referer'=> $visitor->getHttpReferer()
        ));
        $visitor->setLastUrlId($write->lastInsertId());
        return $this;
    }
    
    protected function _beforeSave(Mage_Core_Model_Abstract $visitor)
    {
        if (!$visitor->getIsNewVisitor()) {
            $this->_saveUrlInfo($visitor);
        }
        return $this;
    }
    
    protected function _afterSave(Mage_Core_Model_Abstract $visitor)
    {
        if ($visitor->getIsNewVisitor()) {
            $this->_saveVisitorInfo($visitor);
            $visitor->setIsNewVisitor(false);
        }
        else {
            $this->_saveVisitorUrl($visitor);
            if ($visitor->getIsCustomerLogin() || $visitor->getIsCustomerLogout()) {
                $this->_saveCustomerInfo($visitor);
            }
        }
        return $this;
    }
    
    /**
     * Saving visitor information
     *
     * @param   Mage_Log_Model_Visitor $visitor
     * @return  Mage_Log_Model_Mysql4_Visitor
     */
    protected function _saveVisitorInfo($visitor)
    {
        $write = $this->getConnection('write');
        $data = array(
            'visitor_id'        => $visitor->getId(),
            'http_referer'      => $visitor->getHttpReferer(),
            'http_user_agent'   => $visitor->getHttpUserAgent(),
            'http_accept_charset'=>$visitor->getHttpAcceptCharset(),
            'http_accept_language'=>$visitor->getHttpAcceptLanguage(),
            'server_addr'       => $visitor->getServerAddr(),
            'remote_addr'       => $visitor->getRemoteAddr(),
        );
        
        $write->insert($this->getTable('log/visitor_info'), $data);
        return $this;
    }
    
    /**
     * Saving visitor and url relation
     *
     * @param   Mage_Log_Model_Visitor $visitor
     * @return  Mage_Log_Model_Mysql4_Visitor
     */
    protected function _saveVisitorUrl($visitor)
    {
        $write = $this->getConnection('write');
        $write->insert($this->getTable('log/url_table'), array(
            'url_id'    => $visitor->getLastUrlId(),
            'visitor_id'=> $visitor->getId(),
            'visit_time'=> now(),
        ));
        return $this;
    }
    
    protected function _saveCustomerInfo($visitor)
    {
        
    }
    
    protected function _saveQuoteInfo($visitor)
    {
        
    }
}