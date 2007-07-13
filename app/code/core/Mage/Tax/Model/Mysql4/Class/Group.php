<?php
/**
 * Tax class group resource
 *
 * @package     Mage
 * @subpackage  Tax
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tax_Model_Mysql4_Class_Group
{

    /**
     * resource tables
     */
    protected $_classGroupTable;

    /**
     * resources
     */
    protected $_write;

    protected $_read;

    public function __construct()
    {
        $this->_classGroupTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_class_group');

        $this->_read = Mage::getSingleton('core/resource')->getConnection('tax_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('tax_write');
    }

    public function getIdFieldName()
    {
        return 'class_id';
    }

    public function save($groupObject)
    {
        $groupArray = array(
            'class_parent_id' => $groupObject->getClassParentId(),
            'class_group_id' => $groupObject->getClassGroup()
        );

        $this->_write->insert($this->_classGroupTable, $groupArray);
    }

    public function delete($groupId)
    {
        $condition = $this->_write->quoteInto("{$this->_classGroupTable}.group_id = ?", $groupId);
        $this->_write->delete($this->_classGroupTable, $condition);
    }
}