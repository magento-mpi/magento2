<?php
/**
 * Rating option collection
 *
 * @package     Mage
 * @subpackage  Rating
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Rating_Model_Mysql4_Rating_Option_Collection extends Varien_Data_Collection_Db
{
    protected $_ratingOptionTable;
    protected $_ratingVoteTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('rating_read'));
        $this->_ratingOptionTable   = Mage::getSingleton('core/resource')->getTableName('rating/rating_option');
        $this->_ratingVoteTable     = Mage::getSingleton('core/resource')->getTableName('rating/rating_vote');

        $this->_sqlSelect->from($this->_ratingOptionTable);

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('rating/rating_option'));
    }

    /**
     * add rating filter
     *
     * @param   int|array $rating
     * @return  Varien_Data_Collection_Db
     */
    public function addRatingFilter($rating)
    {
        if (is_numeric($rating)) {
            $this->addFilter('rating_id', $rating);
        }
        elseif (is_array($rating)) {
            $this->addFilter('rating_id', $this->_getConditionSql('rating_id', array('in'=>$rating)), 'string');
        }
        return $this;
    }

    /**
     * set order by position field
     *
     * @param   string $dir
     * @return  Varien_Data_Collection_Db
     */
    public function setPositionOrder($dir='ASC')
    {
        $this->setOrder($this->_ratingOptionTable.'.position', $dir);
        return $this;
    }
}
