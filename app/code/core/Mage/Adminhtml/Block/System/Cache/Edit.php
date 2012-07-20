<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache management edit page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Cache_Edit extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('system/cache/edit.phtml');
        $this->setTitle('Cache Management');
    }

    protected function _prepareLayout()
    {
        $this->setChild('save_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Save Cache Settings'),
                    'onclick'   => 'configForm.submit()',
                    'class' => 'save',
                ))
        );
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
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_System_Cache_Form')
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
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Catalog Rewrites'),
                'buttons'   => array(
                    array(
                        'name'      => 'refresh_catalog_rewrites',
                        'action'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Refresh'),
                        )
                ),
            ),
            'clear_images_cache'         => array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Images Cache'),
                'buttons'   => array(
                    array(
                        'name'      => 'clear_images_cache',
                        'action'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Clear'),
                        )
                ),
            ),
            'rebuild_search_index'      => array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Search Index'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_search_index',
                        'action'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Rebuild'),
                    )
                ),
            ),
            'rebuild_inventory_stock_status' => array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Inventory Stock BugsCoverage'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_inventory_stock_status',
                        'action'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Refresh'),
                    )
                ),
            ),
            'rebuild_catalog_index'         => array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Rebuild Catalog Index'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_catalog_index',
                        'action'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Rebuild'),
                    )
                ),
            ),
            'rebuild_flat_catalog_category' => array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Rebuild Flat Catalog Category'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_flat_catalog_category',
                        'action'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Rebuild'),
                    )
                ),
            ),
            'rebuild_flat_catalog_product' => array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Rebuild Flat Catalog Product'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_flat_catalog_product',
                        'action'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Rebuild'),
                    )
                ),
            ),
        );
    }
}
