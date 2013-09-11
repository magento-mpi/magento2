<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Convert profile edit tab
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tab;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * Initialize Grid block
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_defaultLimit = 200;
        $this->setId('extension_custom_edit_grid');
        $this->setUseAjax(true);
    }

    /**
     * Creates extension collection if it has not been created yet
     *
     * @return \Magento\Connect\Model\Extension\Collection
     */
    public function getCollection()
    {
        if (!$this->_collection) {
            $this->_collection = \Mage::getModel('Magento\Connect\Model\Extension\Collection');
        }
        return $this->_collection;
    }

    /**
     * Prepare Local Package Collection for Grid
     *
     * @return \Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tab\Grid
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->getCollection());
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Magento_Adminhtml_Block_Extension_Custom_Edit_Tab_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('folder', array(
            'header'  => __('Folder'),
            'index'   => 'folder',
            'width'   => 100,
            'type'    => 'options',
            'options' => $this->getCollection()->collectFolders()
        ));

        $this->addColumn('package', array(
            'header' => __('Package'),
            'index'  => 'package',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Self URL getter
     *
     * @return string
     */
    public function getCurrentUrl($params = array())
    {
        if (!isset($params['_current'])) {
            $params['_current'] = true;
        }
        return $this->getUrl('*/*/grid', $params);
    }

    /**
     * Row URL getter
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/load', array('id' => strtr(base64_encode($row->getFilenameId()), '+/=', '-_,')));
    }
}
