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
    protected $_pollId;
    protected $_websiteId;
    protected $_answerCollection;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('poll_read'));

        $this->_pollTable = Mage::getSingleton('core/resource')->getTableName('poll/poll');

        $this->_sqlSelect
            ->from($this->_pollTable);

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('poll/poll'));
    }

    public function loadData($printQuery = false, $logQuery = false)
    {
        parent::loadData($printQuery, $logQuery);
        return $this;
    }

    public function addPollFilter($pollId)
    {
        $this->addFilter('poll_id', $pollId);
        return $this;
    }

    public function addAnswers()
    {
        $arrPollId = $this->getColumnValues('poll_id');
        $this->_getAnswersCollection()
            ->addPollFilter($arrPollId)
            ->loadData();

        foreach( $this->_items as $key => $item ) {
            $item->setAnswers($this->_getAnswersCollection()->getPollAnswers($item));
        }

        return $this;
    }

    protected  function _getAnswersCollection()
    {
        if( !$this->_answerCollection ) {
            $this->_answerCollection = Mage::getResourceModel('poll/poll_answer_collection');
        }
        return $this->_answerCollection;
    }
}