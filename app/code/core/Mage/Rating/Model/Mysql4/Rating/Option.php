<?php
/**
 * Rating option resource model
 *
 * @package     Mage
 * @subpackage  Rating
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Rating_Model_Mysql4_Rating_Option
{
    protected $_ratingOptionTable;
    protected $_ratingVoteTable;

    protected $_read;
    protected $_write;

    public function __construct()
    {
        $this->_ratingOptionTable  = Mage::getSingleton('core/resource')->getTableName('rating/rating_option');
        $this->_ratingVoteTable    = Mage::getSingleton('core/resource')->getTableName('rating/rating_vote');

        $this->_read  = Mage::getSingleton('core/resource')->getConnection('rating_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('rating_write');
    }

    public function save($object)
    {
        if( $object->getId() ) {
            $object->unsetOptionId();
            $this->_write->update($this->_ratingOptionTable, $object->getData());
        } else {
            $condition = $this->_write->quoteInto('option_id = ?', $object->getId());
            $this->_write->insert($this->_ratingOptionTable, $object->getData(), $condition);
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
        if ($action instanceof Mage_Core_Controller_Zend_Action) {
            $data = array(
                'option_id'     => $option->getId(),
                'remote_ip'     => $action->getRequest()->getServer('REMOTE_ADDR'),
                'remote_ip_long'=> ip2long($action->getRequest()->getServer('REMOTE_ADDR')),
                'customer_id'   => Mage::getSingleton('customer/session')->getCustomerId(),
                'entity_pk_value' => $option->getEntityPkValue(),
                'rating_id'     => $option->getRatingId()
            );

            try {
                $this->_write->insert($this->_ratingVoteTable, $data);
            }
            catch (Exception $e){
                throw $e;
            }
        }
    }
}
