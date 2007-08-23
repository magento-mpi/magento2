<?php
/**
 * Tag resourse model
 *
 * @package    Mage
 * @subpackage Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Tag_Model_Mysql4_Tag extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('tag/tag', 'tag_id');
        $this->_uniqueFields = array( array('field' => 'name', 'title' => __('Tag') ) );
    }

    public function loadByName($model, $name)
    {
        if( $name ) {
            $read = $this->getConnection('read');
            $select = $read->select();

            $select->from($this->getMainTable())
                ->where('name = ?', $name);
            $data = $read->fetchRow($select);

            $model->setData( ( is_array($data) ) ? $data : array() );
        } else {
            return false;
        }
    }
}