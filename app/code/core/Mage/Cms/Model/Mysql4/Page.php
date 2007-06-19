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

    function __construct()
    {
        $this->_pageTable = Mage::getSingleton('core/resource')->getTableName('cms_resource', 'page');

        $this->_read = Mage::getSingleton('core/resource')->getConnection('cms_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('cms_write');
    }

    public function load($pageId)
    {
        $condition = $this->_read->quoteInto("{$this->_pageTable}.page_identifier = ?", $pageId);
        $select = $this->_read->select();
        $select->from($this->_pageTable);
        $select->where($condition);

        return $this->_read->fetchOne($select);
    }

}