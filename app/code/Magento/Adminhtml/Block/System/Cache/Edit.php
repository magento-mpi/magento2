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
 * Cache management edit page
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_System_Cache_Edit extends Magento_Adminhtml_Block_Widget
{

    protected $_template = 'system/cache/edit.phtml';

    protected function _construct()
    {
        parent::_construct();

        $this->setTitle('Cache Management');
    }

    protected function _prepareLayout()
    {
        $this->addChild('save_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Save Cache Settings'),
            'class' => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#config-edit-form'),
                ),
            ),
        ));
        return parent::_prepareLayout();
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

    public function initForm()
    {
        $this->setChild('form',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_System_Cache_Form')
                ->initForm()
        );
        return $this;
    }

    /**
     * Retrieve Catalog Tools Data
     *
     * @return array
     */
    public function getCatalogData()
    {
        return array(
            'refresh_catalog_rewrites'   => array(
                'label'     => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Catalog Rewrites'),
                'buttons'   => array(
                    array(
                        'name'      => 'refresh_catalog_rewrites',
                        'action'    => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Refresh'),
                        )
                ),
            ),
            'clear_images_cache'         => array(
                'label'     => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Images Cache'),
                'buttons'   => array(
                    array(
                        'name'      => 'clear_images_cache',
                        'action'    => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Clear'),
                        )
                ),
            ),
            'rebuild_search_index'      => array(
                'label'     => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Search Index'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_search_index',
                        'action'    => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Rebuild'),
                    )
                ),
            ),
            'rebuild_inventory_stock_status' => array(
                'label'     => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Inventory Stock Status'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_inventory_stock_status',
                        'action'    => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Refresh'),
                    )
                ),
            ),
            'rebuild_catalog_index'         => array(
                'label'     => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Rebuild Catalog Index'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_catalog_index',
                        'action'    => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Rebuild'),
                    )
                ),
            ),
            'rebuild_flat_catalog_category' => array(
                'label'     => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Rebuild Flat Catalog Category'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_flat_catalog_category',
                        'action'    => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Rebuild'),
                    )
                ),
            ),
            'rebuild_flat_catalog_product' => array(
                'label'     => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Rebuild Flat Catalog Product'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_flat_catalog_product',
                        'action'    => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Rebuild'),
                    )
                ),
            ),
        );
    }
}
