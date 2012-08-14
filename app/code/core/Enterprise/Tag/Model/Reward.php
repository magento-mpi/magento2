<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tag reward model
 *
 * @category    Enterprise
 * @package     Enterprise_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Tag_Model_Reward extends Enterprise_Reward_Model_Reward
{
    /**
     * Index of reward action when tag submitted
     */
    const REWARD_ACTION_TAG = 7;

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        self::$_actionModelClasses[self::REWARD_ACTION_TAG] = 'Enterprise_Tag_Model_Reward_Action_Tag';
    }
}
