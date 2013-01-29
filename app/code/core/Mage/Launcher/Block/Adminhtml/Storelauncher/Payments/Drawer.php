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
 * Payments Drawer Block
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Payments_Drawer extends Mage_Launcher_Block_Adminhtml_Drawer
{
    /**
     * Field obscured value
     */
    const FIELD_OBSCURED_VALUE = '******';

    /**
     * Get Translated Tile Header
     *
     * @return string
     */
    public function getTileHeader()
    {
        return $this->helper('Mage_Launcher_Helper_Data')->__('Payment Methods');
    }

    /**
     * Retrieve Store Config
     *
     * @param string $path
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return $this->_storeConfig->getConfig($path);
    }

    /**
     * @param string $configPath
     * @return string
     */
    public function getObscuredValue($configPath)
    {
        $value = $this->getConfigValue($configPath);
        if (!empty($value)) {
            return self::FIELD_OBSCURED_VALUE;
        }
        return $value;
    }
}
