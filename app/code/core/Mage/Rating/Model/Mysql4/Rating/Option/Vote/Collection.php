<?php
/**
 * Rating votes collection
 *
 * @package     Mage
 * @subpackage  Rating
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Rating_Model_Mysql4_Rating_Option_Vote_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('rating/rating_option_vote');
    }

    public function setReviewFilter($reviewId)
    {
        $this->_sqlSelect->where("review_id = ?", $reviewId);
        return $this;
    }
}