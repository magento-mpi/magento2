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
namespace Magento\Poll\Model\Resource\Poll;

class Answer extends \Magento\Core\Model\Resource\Db\AbstractDb
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
     * @return \Magento\Poll\Model\Resource\Poll\Answer
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
