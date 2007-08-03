<?php
/**
 * Poll
 *
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

class Mage_Poll_Model_Mysql4_Poll extends Mage_Core_Model_Mysql4_Abstract
{
    function __construct()
    {
        $this->_init('poll/poll', 'poll_id');
    }

    public function resetVotesCount($object)
    {
        $read = $this->getConnection('read');
        $select = $read->select();
        $select->from($this->getTable('poll_answer'), new Zend_Db_Expr("SUM(votes_count)"))
            ->where("poll_id = ?", $object->getPollId());

        $count = $read->fetchOne($select);

        $write = $this->getConnection('write');
        $condition = $write->quoteInto("{$this->getIdFieldName()} = ?", $object->getPollId());
        $write->update($this->getMainTable(), array('votes_count' => $count), $condition);
        return $object;
    }

    public function getRandomId()
    {
        $read = $this->getConnection('read');
        $select = $read->select();

        $select->from($this->getMainTable(), $this->getIdFieldName())
            ->where('active = ?', 1)
            ->where('closed = ?', 0)
            ->order(new Zend_Db_Expr('RAND()'));

        return $read->fetchOne($select);
    }
}