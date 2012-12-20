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
 * SEO Drawer Block
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Promotestore_Seo_Drawer extends Mage_Launcher_Block_Adminhtml_Drawer
{
    /**
     * Template for SEO Tile Block
     *
     * @var string
     */
    protected $_template = 'page/promotestore/tile/seo_drawer.phtml';

    /**
     * Get Translated Tile Header
     *
     * @return string
     */
    public function getTileHeader()
    {
        return $this->helper('Mage_Launcher_Helper_Data')->__('SEO');
    }
}
