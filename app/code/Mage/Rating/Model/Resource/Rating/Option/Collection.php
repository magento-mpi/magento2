<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Rating option collection
 *
 * @category    Mage
 * @package     Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rating_Model_Resource_Rating_Option_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Rating votes table
     *
     * @var string
     */
    protected $_ratingVoteTable;

    /**
     * Define model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Rating_Model_Rating_Option', 'Mage_Rating_Model_Resource_Rating_Option');
        $this->_ratingVoteTable     = $this->getTable('rating_option_vote');
    }

    /**
     * Add rating filter
     *
     * @param   int|array $rating
     * @return  Mage_Rating_Model_Resource_Rating_Option_Collection
     */
    public function addRatingFilter($rating)
    {
        if (is_numeric($rating)) {
            $this->addFilter('rating_id', $rating);
        } elseif (is_array($rating)) {
            $this->addFilter('rating_id', $this->_getConditionSql('rating_id', array('in'=>$rating)), 'string');
        }
        return $this;
    }

    /**
     * Set order by position field
     *
     * @param   string $dir
     * @return  Mage_Rating_Model_Resource_Rating_Option_Collection
     */
    public function setPositionOrder($dir='ASC')
    {
        $this->setOrder('main_table.position', $dir);
        return $this;
    }
}
