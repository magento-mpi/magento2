<?php
/**
 * Rating model
 *
 * @package     Mage
 * @subpackage  Rating
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Rating_Model_Mysql4_Rating extends Mage_Core_Model_Mysql4_Abstract
{
    public function __construct()
    {
        $this->_init('rating/rating', 'rating_id');
    }

    public function getEntitySummary($object)
    {
        $read = $this->getConnection('read');
        $sql = "SELECT
                    SUM({$this->getTable('rating_vote')}.percent) as sum,
                    COUNT(*) as count
                FROM
                    {$this->getTable('rating_vote')}
                WHERE
                    {$read->quoteInto($this->getTable('rating_vote').'.entity_pk_value=?', $object->getEntityPkValue())}
                GROUP BY
                    {$this->getTable('rating_vote')}.entity_pk_value";
        $data = $read->fetchRow($sql);

        $object->addData( (is_array($data)) ? $data : array() );
        return $object;
    }

    public function getReviewSummary($object)
    {
        $read = $this->getConnection('read');
        $sql = "SELECT
                    SUM({$this->getTable('rating_vote')}.percent) as sum,
                    COUNT(*) as count
                FROM
                    {$this->getTable('rating_vote')}
                WHERE
                    {$read->quoteInto($this->getTable('rating_vote').'.review_id=?', $object->getReviewId())}
                GROUP BY
                    {$this->getTable('rating_vote')}.review_id";

        $data = $read->fetchRow($sql);

        $object->addData( (is_array($data)) ? $data : array() );
        return $object;
    }
}