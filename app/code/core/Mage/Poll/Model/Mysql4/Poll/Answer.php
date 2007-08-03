<?php
/**
 * Poll answers resource model
 *
 * @package     Mage
 * @subpackage  Poll
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Poll_Model_Mysql4_Poll_Answer extends Mage_Core_Model_Mysql4_Abstract
{
    function __construct()
    {
        $this->_init('poll/poll_answer', 'answer_id');
        $this->_uniqueFields = array(array('field' => array('answer_title', 'poll_id'), 'title' => __('Answer with the same title in this poll')));
    }
}