<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Block\Adminhtml;

/**
 * Adminhtml cms blocks content block
 *
 * @category   Magento
 * @package    Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Block extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Cms';
        $this->_controller = 'adminhtml_block';
        $this->_headerText = __('Static Blocks');
        $this->_addButtonLabel = __('Add New Block');
        parent::_construct();
    }

}
