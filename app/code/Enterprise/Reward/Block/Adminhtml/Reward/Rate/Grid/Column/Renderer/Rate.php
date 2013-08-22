<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward rate grid renderer
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Adminhtml_Reward_Rate_Grid_Column_Renderer_Rate
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        $websiteId = $row->getWebsiteId();
        return Enterprise_Reward_Model_Reward_Rate::getRateText($row->getDirection(), $row->getPoints(),
            $row->getCurrencyAmount(),
            0 == $websiteId ? null : Mage::app()->getWebsite($websiteId)->getBaseCurrencyCode()
        );
    }
}
