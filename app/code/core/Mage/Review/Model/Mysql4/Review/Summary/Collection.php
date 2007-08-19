<?php
/**
 * Review summery collection
 *
 * @package     Mage
 * @subpackage  Review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Review_Model_Mysql4_Review_Summary_Collection extends Varien_Data_Collection_Db
{
    protected $_summaryTable;

    public function __construct()
    {
        $resources = Mage::getSingleton('core/resource');

        parent::__construct($resources->getConnection('review_read'));
        $this->_summaryTable = $resources->getTableName('review/review_aggregate');

        $this->_sqlSelect->from($this->_summaryTable);
    }

    public function addEntityFilter($entityId, $entityType=1)
    {
        $this->_sqlSelect->where('entity_pk_value IN(?)', $entityId)
            ->where('entity_type = ?', $entityType);
        return $this;
    }
}