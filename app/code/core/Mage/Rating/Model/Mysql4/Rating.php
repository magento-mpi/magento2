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
 * @package    Mage_Rating
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating model
 *
 * @category   Mage
 * @package    Mage_Rating
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Rating_Model_Mysql4_Rating extends Mage_Core_Model_Mysql4_Abstract
{
    public function __construct()
    {
        $this->_init('rating/rating', 'rating_id');
        $this->_uniqueFields = array( array('field' => 'rating_code', 'title' => __('Rating with the same title') ) );
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