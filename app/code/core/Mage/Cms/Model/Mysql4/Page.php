<?php
/**
 * Cms page mysql resource
 *
 * @package     Mage
 * @subpackage  Cms
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Cms_Model_Mysql4_Page
{

    protected $_pageTable;

    protected $_pageData;

    protected $_pageId;

    function __construct()
    {
        $this->_pageTable = Mage::getSingleton('core/resource')->getTableName('cms_resource', 'page');

        $this->_read = Mage::getSingleton('core/resource')->getConnection('cms_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('cms_write');
    }

    public function load($pageId, $reload=null)
    {
        if( $pageId != $this->_pageId ) {
            $this->_pageData = null;
            $this->_pageId = $pageId;
            $reload = true;
        }

        $condition = $this->_read->quoteInto("{$this->_pageTable}.page_identifier = ?", $pageId);
        $select = $this->_read->select();
        $select->from($this->_pageTable);
        $select->where($condition);

        $this->_pageData = ( $reload === true ) ? $this->_read->fetchRow($select) : $this->_pageData;
        return $this->_pageData;
    }

    public function isDisabled($pageId)
    {
        $this->load($pageId);

        if( $this->_pageData['page_active'] == 0 ) {
            return true;
        } else {
            return false;
        }
    }

    public function save($page)
    {
        if( $page->getPageId() ) {
            $condition = $this->_write->quoteInto("{$this->_pageTable}.page_id=?", $page->getPageId());
            $page->setPageCreationTime(new Zend_Db_Expr('NOW()'));
            $this->_write->update($this->_pageTable, $page->getData(), $condition);
        } else {
            $page->setPageUpdateTime(new Zend_Db_Expr('NOW()'));
            $this->_write->insert($this->_pageTable, $page->getData());
        }
        return $this;
    }

    public function disablePage($pageId)
    {
        $page = $this->load($pageId);
        $page->setPageActive(0);
        $this->save($page);
    }

    public function enablePage($pageId)
    {
        $page = $this->load($pageId);
        $page->setPageActive(1);
        $this->save($page);
    }
}