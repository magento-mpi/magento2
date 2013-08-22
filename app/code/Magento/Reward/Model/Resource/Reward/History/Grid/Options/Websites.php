<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Reward_Model_Resource_Reward_History_Grid_Options_Websites
    implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * System Store Model
     *
     * @var Magento_Reward_Model_Source_Website
     */
    protected $_systemStore;

    /**
     * @param Magento_Reward_Model_Source_Website
     */
    public function __construct(Magento_Reward_Model_Source_Website $systemStore)
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
