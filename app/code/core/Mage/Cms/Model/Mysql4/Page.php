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

    protected $_write;

    protected $_read;

    function __construct()
    {
        $this->_pageTable = Mage::getSingleton('core/resource')->getTableName('cms/page');

        $this->_read = Mage::getSingleton('core/resource')->getConnection('cms_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('cms_write');
    }

    public function load($pageId)
    {
        $condition = $this->_read->quoteInto("{$this->_pageTable}.page_identifier = ?", $pageId);
        $select = $this->_read->select();
        $select->from($this->_pageTable);
        $select->where($condition);

        return $this->_read->fetchRow($select);
    }

    public function loadById($pageId=0)
    {
        $condition = $this->_read->quoteInto("{$this->_pageTable}.page_id = ?", $pageId);
        $select = $this->_read->select();
        $select->from($this->_pageTable);
        $select->where($condition);

        return $this->_read->fetchRow($select);
    }

    public function save($page)
    {
        if( $page->getPageId() ) {
            $condition = $this->_write->quoteInto("{$this->_pageTable}.page_id=?", $page->getPageId());
            $page->setPageCreationTime(now());
            $this->_write->update($this->_pageTable, $page->getData(), $condition);
        } else {
            $page->setPageUpdateTime(now());
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

    public function delete($pageId)
    {
        $condition = $this->_write->quoteInto("{$this->_pageTable}.page_id=?", $pageId);
        $this->_write->delete($this->_pageTable, $condition);
        return $this;
    }
}
