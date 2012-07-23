<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml catalog (google) sitemaps block
 *
 * @category   Mage
 * @package    Mage_Sitemap
 */
class Mage_Adminhtml_Block_Sitemap extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_controller = 'sitemap';
        $this->_headerText = Mage::helper('Mage_Sitemap_Helper_Data')->__('XML Sitemap');
        $this->_addButtonLabel = Mage::helper('Mage_Sitemap_Helper_Data')->__('Add Sitemap');
        parent::__construct();
    }

}
