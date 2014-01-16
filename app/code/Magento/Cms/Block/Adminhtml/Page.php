<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml cms pages content block
 *
 * @category   Magento
 * @package    Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cms\Block\Adminhtml;

class Page extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Block constructor
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_page';
        $this->_blockGroup = 'Magento_Cms';
        $this->_headerText = __('Manage Pages');

        parent::_construct();

        if ($this->_isAllowedAction('Magento_Cms::save')) {
            $this->_updateButton('add', 'label', __('Add New Page'));
        } else {
            $this->_removeButton('add');
        }

    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

}
