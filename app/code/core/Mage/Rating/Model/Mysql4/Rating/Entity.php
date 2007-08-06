<?php
/**
 * Rating entity resource
 *
 * @package     Mage
 * @subpackage  Rating
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Rating_Model_Mysql4_Rating_Entity extends Mage_Core_Model_Mysql4_Abstract
{
    function __construct()
    {
        $this->_init('rating/rating_entity', 'entity_id');
    }

    public function getIdByCode($entityCode)
    {
        $read = $this->getConnection('read');
        $select = $read->select();
        $select->from($this->getTable('rating_entity'), $this->getIdFieldName())
            ->where('entity_code = ?', $entityCode);
        return $read->fetchOne($select);
    }
}