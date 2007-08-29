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
 * Rating option resource model
 *
 * @category   Mage
 * @package    Mage_Rating
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Rating_Model_Mysql4_Rating_Option
{
    protected $_ratingOptionTable;
    protected $_ratingVoteTable;
    protected $_aggregateTable;

    protected $_read;
    protected $_write;

    protected $_optionData;
    protected $_optionId;

    public function __construct()
    {
        $this->_ratingOptionTable  = Mage::getSingleton('core/resource')->getTableName('rating/rating_option');
        $this->_ratingVoteTable    = Mage::getSingleton('core/resource')->getTableName('rating/rating_vote');
        $this->_aggregateTable    = Mage::getSingleton('core/resource')->getTableName('rating/rating_vote_aggregated');

        $this->_read  = Mage::getSingleton('core/resource')->getConnection('rating_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('rating_write');
    }

    public function save($object)
    {
        if( $object->getId() ) {
            $condition = $this->_write->quoteInto('option_id = ?', $object->getId());
            $object->unsetData('option_id');
            $this->_write->update($this->_ratingOptionTable, $object->getData(), $condition);
        } else {
            $this->_write->insert($this->_ratingOptionTable, $object->getData());
        }
        return $object;
    }

    public function delete($object)
    {
        $condition = $this->_write->quoteInto('option_id = ?', $object->getId());
        $this->_write->delete($this->_ratingOptionTable, $condition);
    }

    public function addVote($option)
    {
        $action = Mage::registry('action');

        if ($action instanceof Mage_Core_Controller_Front_Action || $action instanceof Mage_Adminhtml_Controller_Action) {
            $optionData = $this->load($option->getId());
            $data = array(
                'option_id'     => $option->getId(),
                'review_id'     => $option->getReviewId(),
                'percent'       => (($optionData['value'] / 5) * 100)
            );

            if( !$option->getDoUpdate() ) {
                $data['remote_ip'] = $action->getRequest()->getServer('REMOTE_ADDR');
                $data['remote_ip_long'] = ip2long($action->getRequest()->getServer('REMOTE_ADDR'));
                $data['customer_id'] = Mage::getSingleton('customer/session')->getCustomerId();
                $data['entity_pk_value'] = $option->getEntityPkValue();
                $data['rating_id'] = $option->getRatingId();
            }

            $this->_write->beginTransaction();
            try {
                if( $option->getDoUpdate() ) {
                    $condition = "vote_id = '{$option->getVoteId()}' AND review_id = '{$option->getReviewId()}'";
                    $this->_write->update($this->_ratingVoteTable, $data, $condition);
                } else {
                    $this->_write->insert($this->_ratingVoteTable, $data);
                    $this->aggregate($option);
                }
                $this->_write->commit();
            }
            catch (Exception $e){
                $this->_write->rollback();
                throw new Exception($e->getMessage());
            }
        }
        return $this;
    }

    public function aggregate($option)
    {
        $select = $this->_read->select();
        $select->from($this->_aggregateTable)
            ->where("{$this->_aggregateTable}.rating_id = ?", $option->getRatingId())
            ->where("{$this->_aggregateTable}.entity_pk_value = ?", $option->getEntityPkValue());

        $oldData = $this->_read->fetchRow($select);
        $optionData = $this->load($option->getId());

        if( $oldData['primary_id'] > 0 ) {
            $option->setVoteCount(new Zend_Db_Expr('vote_count + 1'))
                ->setVoteValueSum( new Zend_Db_Expr('vote_value_sum + ' . $optionData['value']) )
                ->setPercent( ((($oldData['vote_value_sum'] / 100) / (($oldData['vote_count'] * 5) / 100)) * 100) )
                ->unsetData('option_id')
                ->unsetData('review_id');

            $condition = $this->_write->quoteInto("{$this->_aggregateTable}.primary_id = ?", $oldData['primary_id']);
            $this->_write->update($this->_aggregateTable, $option->getData(), $condition);
        } else {
            $option->setVoteCount('1')
                ->setVoteValueSum( $optionData['value'] )
                ->setPercent( (($optionData['value'] / 5) * 100) )
                ->unsetData('option_id')
                ->unsetData('review_id');

            $this->_write->insert($this->_aggregateTable, $option->getData());
        }
    }

    public function load($optionId)
    {
        if( !$this->_optionData || $this->_optionId != $optionId ) {
            $select = $this->_read->select();
            $select->from($this->_ratingOptionTable)
                ->where('option_id = ?', $optionId);

            $data = $this->_read->fetchRow($select);

            $this->_optionData = $data;
            $this->_optionId = $optionId;
            return $data;
        }

        return $this->_optionData;
    }
}
