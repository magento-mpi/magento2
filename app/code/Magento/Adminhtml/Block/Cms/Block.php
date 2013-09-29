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
 * Adminhtml cms blocks content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Cms;

class Block extends \Magento\Adminhtml\Block\Widget\Grid\Container
{

    protected function _construct()
    {
        $this->_controller = 'cms_block';
        $this->_headerText = __('Static Blocks');
        $this->_addButtonLabel = __('Add New Block');
        parent::_construct();
    }

}
