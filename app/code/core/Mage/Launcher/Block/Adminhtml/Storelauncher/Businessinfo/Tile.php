<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * BusinessInfo Tile Block
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Tile extends Mage_Launcher_Block_Adminhtml_Tile
{
    /**
     * Template for Businessinfo Tile Block
     *
     * @var string
     */
    protected $_template = 'page/storelauncher/tile/businessinfo.phtml';

    /**
     * Get Address
     *
     * @return string
     */
    public function getAddress()
    {
        $address = $this->_storeConfig->getConfig('general/store_information/address');
        $addressValues = explode("\n", $address);
        $email = $this->_storeConfig->getConfig('trans_email/ident_general/email');
        $addressValues[] = $email;
        return $addressValues;
    }
}
