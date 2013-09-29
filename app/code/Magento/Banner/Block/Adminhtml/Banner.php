<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Block\Adminhtml;

class Banner extends \Magento\Adminhtml\Block\Widget\Grid\Container
{

    /**
     * Initialize banners manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_banner';
        $this->_blockGroup = 'Magento_Banner';
        $this->_headerText = __('Banners');
        $this->_addButtonLabel = __('Add Banner');
        parent::_construct();
    }
}
