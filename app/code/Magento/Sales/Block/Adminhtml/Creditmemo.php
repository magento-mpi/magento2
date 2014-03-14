<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml;

/**
 * Adminhtml sales creditmemos block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Creditmemo extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_creditmemo';
        $this->_blockGroup = 'Magento_Sales';
        $this->_headerText = __('Credit Memos');
        parent::_construct();
        $this->_removeButton('add');
    }

}
