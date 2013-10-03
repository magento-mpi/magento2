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
 * Adminhtml store content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\System\Store;

class Store extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Magento_Adminhtml';

    protected function _construct()
    {
        $this->_controller  = 'system_store';
        $this->_headerText  = __('Stores');
        parent::_construct();
    }

    protected function _prepareLayout()
    {
        /* Update default add button to add website button */
        $this->_updateButton('add', 'label', __('Create Website'));
        $this->_updateButton('add', 'onclick', "setLocation('" . $this->getUrl('*/*/newWebsite') . "')");

        /* Add Store Group button */
        $this->_addButton('add_group', array(
            'label'     => __('Create Store'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/newGroup') .'\')',
            'class'     => 'add',
        ));

        /* Add Store button */
        $this->_addButton('add_store', array(
            'label'   => __('Create Store View'),
            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/newStore') . '\')',
            'class'   => 'add',
        ));

        return parent::_prepareLayout();
    }
}
