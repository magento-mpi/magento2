<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache management edit page
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\System\Cache;

class Edit extends \Magento\Backend\Block\Widget
{

    protected $_template = 'Magento_Backend::system/cache/edit.phtml';

    protected function _construct()
    {
        parent::_construct();

        $this->setTitle('Cache Management');
    }

    protected function _prepareLayout()
    {
        $this->addChild('save_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Save Cache Settings'),
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
        return $this->getUrl('adminhtml/*/save', array('_current'=>true));
    }

    public function initForm()
    {
        $this->setChild('form',
            $this->getLayout()->createBlock('Magento\Backend\Block\System\Cache\Form')
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
                'label'     => __('Catalog Rewrites'),
                'buttons'   => array(
                    array(
                        'name'      => 'refresh_catalog_rewrites',
                        'action'    => __('Refresh'),
                        )
                ),
            ),
            'clear_images_cache'         => array(
                'label'     => __('Images Cache'),
                'buttons'   => array(
                    array(
                        'name'      => 'clear_images_cache',
                        'action'    => __('Clear'),
                        )
                ),
            ),
            'rebuild_search_index'      => array(
                'label'     => __('Search Index'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_search_index',
                        'action'    => __('Rebuild'),
                    )
                ),
            ),
            'rebuild_inventory_stock_status' => array(
                'label'     => __('Inventory Stock Status'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_inventory_stock_status',
                        'action'    => __('Refresh'),
                    )
                ),
            ),
            'rebuild_catalog_index'         => array(
                'label'     => __('Rebuild Catalog Index'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_catalog_index',
                        'action'    => __('Rebuild'),
                    )
                ),
            ),
            'rebuild_flat_catalog_category' => array(
                'label'     => __('Rebuild Flat Catalog Category'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_flat_catalog_category',
                        'action'    => __('Rebuild'),
                    )
                ),
            ),
            'rebuild_flat_catalog_product' => array(
                'label'     => __('Rebuild Flat Catalog Product'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_flat_catalog_product',
                        'action'    => __('Rebuild'),
                    )
                ),
            ),
        );
    }
}
