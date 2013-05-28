<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payments Drawer Block
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_Drawer extends Saas_Launcher_Block_Adminhtml_Drawer
{
    /**
     * Get Translated Tile Header
     *
     * @return string
     */
    public function getTileHeader()
    {
        return $this->helper('Saas_Launcher_Helper_Data')->__('Payments');
    }

    /**
     * @todo Will be replaced in #MAGETWO-10159
     * @return string
     */
    public function getMoreUrl()
    {
        return
            'https://www.paypal.com/webapps/mpp/referral/paypal-express-checkout?partner_id=NB9WWHYEMVUMS';
    }
}
