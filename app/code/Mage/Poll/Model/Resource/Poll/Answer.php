<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Poll
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Poll answers resource model
 *
 * @category    Mage
 * @package     Mage_Poll
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Poll_Model_Resource_Poll_Answer extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize Poll_Answer resource
     *
     */
    protected function _construct()
    {
        $this->_init('poll_answer', 'answer_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Poll_Model_Resource_Poll_Answer
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => array('answer_title', 'poll_id'),
            'title' => __('You already used this answer title.')
        ));
        return $this;
    }
}
