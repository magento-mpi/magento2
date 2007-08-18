<?php
/**
 * Tags customer collection
 *
 * @package     Mage
 * @subpackage  Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tag_Model_Mysql4_Customer_Collection extends Mage_Customer_Model_Entity_Customer_Collection
{
    public function __construct()
    {
        return parent::__construct();
    }

    public function addTagFilter($tagId)
    {
        $this->getSelect()
            ->where('tr.tag_id = ?', $tagId);
        return $this;
    }

    public function resetSelect()
    {
        parent::resetSelect();
        $this->_joinFields();
        return $this;
    }

    protected function _joinFields()
    {
        $tagRelationTable = Mage::getSingleton('core/resource')->getTableName('tag/relation');

        $this->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addAttributeToSelect('email');

        $this->getSelect()
            ->join(array('tr' => $tagRelationTable), "tr.customer_id = e.entity_id");
    }
}