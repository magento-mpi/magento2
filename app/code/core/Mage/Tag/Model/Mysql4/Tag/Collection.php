<?php
/**
 * Tag collection model
 *
 * @package    Mage
 * @subpackage Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Tag_Model_Mysql4_Tag_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected $_tagRelTable;

    protected function _construct()
    {
        $this->_init('tag/tag');
        $this->_tagRelTable = $this->getTable('tag/relation');
    }

    public function load($printQuery = false, $logQuery = false)
    {
        return parent::load($printQuery, $logQuery);
    }

    public function setStatusFilter($status)
    {
        $this->getSelect()->where('main_table.status = ?', $status);
        return $this;
    }

    public function addPopularity($limit=null)
    {
        $this->getSelect()
            ->joinLeft($this->_tagRelTable, 'main_table.tag_id='.$this->_tagRelTable.'.tag_id', array('*', 'popularity' => 'COUNT('.$this->_tagRelTable.'.tag_relation_id)'))
            ->group('main_table.tag_id');
        if (! is_null($limit)) {
            $this->getSelect()->limit($limit);
        }
        $this->setOrder('popularity', 'DESC');
        return $this;
    }

}
