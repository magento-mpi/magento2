<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward rate grid renderer
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Block_Adminhtml_Reward_Rate_Grid_Column_Renderer_Rate
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @var Magento_Reward_Model_Reward_Rate
     */
    protected $_rewardRate;

    /**
     * @param Magento_Reward_Model_Reward_Rate $rewardRate
     * @param Magento_Backend_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Reward_Model_Reward_Rate $rewardRate,
        Magento_Backend_Block_Context $context,
        array $data = array()
    ) {
        $this->_rewardRate = $rewardRate;
        parent::__construct($context, $data);
    }

    /**
     * Renders grid column
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        $websiteId = $row->getWebsiteId();
        return $this->_rewardRate->getRateText($row->getDirection(), $row->getPoints(),
            $row->getCurrencyAmount(),
            0 == $websiteId ? null : Mage::app()->getWebsite($websiteId)->getBaseCurrencyCode()
        );
    }
}
