<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Poll
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Poll answers resource model
 *
 * @category    Magento
 * @package     Magento_Poll
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Poll_Model_Resource_Poll_Answer extends Magento_Core_Model_Resource_Db_Abstract
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
     * @return Magento_Poll_Model_Resource_Poll_Answer
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
