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
namespace Magento\Sitemap\Block\Adminhtml;

class Sitemap extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * Block constructor
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_sitemap';
        $this->_blockGroup = 'Magento_Sitemap';
        $this->_headerText = __('XML Sitemap');
        $this->_addButtonLabel = __('Add Sitemap');
        parent::_construct();
    }

}
