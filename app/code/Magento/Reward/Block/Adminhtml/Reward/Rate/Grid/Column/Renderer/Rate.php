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
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Reward_Model_Reward_Rate
     */
    protected $_rate;

    /**
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Reward_Model_Reward_Rate $rate
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Reward_Model_Reward_Rate $rate,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_rate = $rate;
        parent::__construct($context, $data);
    }

    /**
     * Renders grid column
     *
     * @param Magento_Object $row
     * @return string
     */
    public function render(Magento_Object $row)
    {
        $websiteId = $row->getWebsiteId();
        return $this->_rate->getRateText(
            $row->getDirection(),
            $row->getPoints(),
            $row->getCurrencyAmount(),
            0 == $websiteId ? null : $this->_storeManager->getWebsite($websiteId)->getBaseCurrencyCode()
        );
    }
}
