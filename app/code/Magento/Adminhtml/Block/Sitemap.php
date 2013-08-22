<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml catalog (google) sitemaps block
 *
 * @category   Magento
 * @package    Magento_Sitemap
 */
class Magento_Adminhtml_Block_Sitemap extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Block constructor
     */
    protected function _construct()
    {
        $this->_controller = 'sitemap';
        $this->_headerText = __('XML Sitemap');
        $this->_addButtonLabel = __('Add Sitemap');
        parent::_construct();
    }

}
