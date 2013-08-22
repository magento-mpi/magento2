<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Reward_Model_Resource_Reward_History_Grid_Options_Websites
    implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * System Store Model
     *
     * @var Enterprise_Reward_Model_Source_Website
     */
    protected $_systemStore;

    /**
     * @param Enterprise_Reward_Model_Source_Website
     */
    public function __construct(Enterprise_Reward_Model_Source_Website $systemStore)
    {
        $this->_systemStore = $systemStore;
    }

    /**
     * Return websites array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->toOptionArray(false);
    }
}
