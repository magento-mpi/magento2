<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Wrapping Adminhtml Block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Block\Adminhtml;

class Giftwrapping extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize gift wrapping management page
     *
     * @return void
     */
    public function _construct()
    {
        $this->_controller = 'adminhtml_giftwrapping';
        $this->_blockGroup = 'Magento_GiftWrapping';
        $this->_headerText = __('Gift Wrapping');
        $this->_addButtonLabel = __('Add Gift Wrapping');
        parent::_construct();
    }
}
