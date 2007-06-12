<?php
/**
 * Pool Mysql4 collection model resource
 *
 * @package     Mage
 * @subpackage  Poll
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Poll_Model_Mysql4_Poll_Collection extends Varien_Data_Collection_Db
{
    protected $_pollTable;
    protected $_pollAnswerTable;
    protected $_pollId;

    public function __construct()
    {
        parent::__construct(Mage::registry('resources')->getConnection('poll_read'));

        $this->_pollTable       = Mage::registry('resources')->getTableName('poll_resource', 'poll');
        $this->_pollAnswerTable = Mage::registry('resources')->getTableName('poll_resource', 'poll_answer');

        $this->_sqlSelect
            ->from($this->_pollTable);

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('poll/poll'));
    }

    protected function _loadAnswers()
    {
        $arrPollId = $this->getColumnValues('poll_id');
        if ($arrPollId) {
            $this->getAnswers($arrPollId);
        }
    }

    public function loadData($pollId)
    {
        parent::loadData();
        foreach ($this->_items as $key => $item) {
            $this->setPollId($key);
            $item->getAnswers();
        }
        return $this->_items;
    }

    public function setPollId($id)
    {
        $this->_pollId = $id;
    }
}