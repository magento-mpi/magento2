<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\System\Store;

/**
 * Adminhtml store content block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Store extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Magento_Adminhtml';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Backend';
        $this->_controller  = 'system_store';
        $this->_headerText  = __('Stores');
        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        /* Update default add button to add website button */
        $this->_updateButton('add', 'label', __('Create Website'));
        $this->_updateButton('add', 'onclick', "setLocation('" . $this->getUrl('adminhtml/*/newWebsite') . "')");

        /* Add Store Group button */
        $this->_addButton('add_group', array(
            'label'     => __('Create Store'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('adminhtml/*/newGroup') .'\')',
            'class'     => 'add',
        ));

        /* Add Store button */
        $this->_addButton('add_store', array(
            'label'   => __('Create Store View'),
            'onclick' => 'setLocation(\'' . $this->getUrl('adminhtml/*/newStore') . '\')',
            'class'   => 'add',
        ));

        return parent::_prepareLayout();
    }
}
