<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Poll
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Poll
 *
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

class Mage_Poll_Model_Mysql4_Poll extends Mage_Core_Model_Mysql4_Abstract
{
    function __construct()
    {
        $this->_init('poll/poll', 'poll_id');
        $this->_uniqueFields = array( array('field' => 'poll_title', 'title' => __('Poll with the same question') ) );
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

    public function getRandomId($object)
    {
        $read = $this->getConnection('read');
        $select = $read->select();

        if( $object->getExcludeFilter() ) {
            $select->where('poll_id NOT IN(?)', $object->getExcludeFilter());
        }

        $select->from($this->getMainTable(), $this->getIdFieldName())
            #->where('active = ?', 1)
            ->where('closed = ?', 0)
            ->order(new Zend_Db_Expr('RAND()'));

        return $read->fetchOne($select);
    }

    public function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $write = $this->getConnection('write');

        try {
            foreach ($object->getAnswers() as $answer) {
                $answer->setPollId($object->getId());
            	$answer->save();
            }
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }
    }
}