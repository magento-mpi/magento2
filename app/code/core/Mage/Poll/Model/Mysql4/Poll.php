<?php
/**
 * Poll
 *
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

class Mage_Poll_Model_Mysql4_Poll
{
    protected $_pollTable;

    protected $_read;
    protected $_write;

    protected $_pollId;

    protected $_poll;

    function __construct()
    {
        $this->_pollTable = Mage::getSingleton('core/resource')->getTableName('poll_resource', 'poll');

        $this->_read = Mage::getSingleton('core/resource')->getConnection('poll_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('poll_write');
    }

    function save($poll)
    {
        if( $poll->getPollId() ) {
            $condition = $this->_write->quoteInto("{$this->_pollTable}.poll_id=?", $poll->getPollId());
            $this->_write->update($this->_pollTable, $poll->getData(), $condition);
        } else {
            $this->_write->insert($this->_pollTable, $poll->getData());
        }

        return $this;
    }

    function delete($poll)
    {
        if( $poll->getPollId() ) {
            $condition = $this->_write->quoteInto("{$this->_pollTable}.poll_id=?", $poll->getPollId());
            $this->_write->delete($this->_pollTable, $condition);
        }

        return $this;
    }

    function load($pollId=null)
    {
        if( isset($pollId) ) {
        	$this->setId($pollId);
        }

        if( $this->getId() ) {
            $condition = $this->_read->quoteInto("{$this->_pollTable}.poll_id=?", $this->getId());

            $select = $this->_read->select();
            $select->from($this->_pollTable);
            $select->where($condition);

            $this->_poll = $this->_read->fetchRow($select);
        }
        return $this;
    }

    function setId($pollId)
    {
        $this->_pollId = intval($pollId);
        return $this;
    }

    function getId()
    {
        return $this->_pollId;
    }
}