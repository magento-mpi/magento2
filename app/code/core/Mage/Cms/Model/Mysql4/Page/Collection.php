<?php
/**
 * CMS page collection
 *
 * @package     Mage
 * @subpackage  Cms
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Cms_Model_Mysql4_Page_Collection extends Varien_Data_Collection_Db
{
    /**
     * Page data table name
     *
     * @var string
     */
    protected $_pageTable;

    /**
     * Construct
     *
     */
    function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('cms_read'));
        $this->_pageTable = Mage::getSingleton('core/resource')->getTableName('cms/page');

        $this->_sqlSelect->from($this->_pageTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('cms/page'));
    }

    public function addEnabledFilter()
    {
        $condition = $this->getConnection()->quoteInto("{$this->_pageTable}.page_active = ?", 1);
        $this->_sqlSelect->where($condition);
        return $this;
    }

    public function addDisabledFilter()
    {
        $condition = $this->getConnection()->quoteInto("{$this->_pageTable}.page_active = ?", 0);
        $this->_sqlSelect->where($condition);
        return $this;
    }
}