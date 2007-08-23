<?php
/**
 * admin product edit tabs
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('product_info_tabs');
        $this->setDestElementId('product_edit_form');
        $this->setTitle(__('Product Information'));
    }

    protected function _initChildren()
    {
        $product = Mage::registry('product');

        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if (!($superAttributes = $product->getSuperAttributesIds())) {
            $superAttributes = false;
        }

        if ($setId && (!$product->isSuperConfig() || $superAttributes !== false ) ) {
            $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
                ->setAttributeSetFilter($setId)
                ->load();

            foreach ($groupCollection as $group) {
                $attributes = $product->getAttributes($group->getId(), true);
                // do not add grops without attributes
                if (count($attributes)==0) {
                    continue;
                }

                $this->addTab('group_'.$group->getId(), array(
                    'label'     => __($group->getAttributeGroupName()),
                    'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_attributes')
                        ->setGroup($group)
                        ->setGroupAttributes($attributes)
                        ->toHtml(),
                ));
            }

            $this->addTab('stores', array(
                'label'     => __('Stores'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_stores')->toHtml(),
            ));

            $this->addTab('categories', array(
                'label'     => __('Categories'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_categories')->toHtml(),
            ));

            $this->addTab('related', array(
                'label'     => __('Related Products'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_related', 'admin.related.products')->toHtml(),
            ));

            $this->addTab('upsell', array(
                'label'     => __('Up-sells'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_upsell', 'admin.upsell.products')->toHtml(),
            ));

            $this->addTab('crosssell', array(
                'label'     => __('Cross-sells'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_crosssell', 'admin.crosssell.products')->toHtml(),
            ));

            if( $this->getRequest()->getParam('id', false) ) {
                $this->addTab('reviews', array(
                    'label'     => __('Product Reviews'),
                    'content'   => $this->getLayout()->createBlock('adminhtml/review_grid', 'admin.product.reviews')
                            ->setProductId($this->getRequest()->getParam('id'))
                            ->setUseAjax(true)
                            ->toHtml(),
                ));

                $this->addTab('tags', array(
                    'label'     => __('Product Tags'),
                    'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_tag', 'admin.product.tags')
                            ->setProductId($this->getRequest()->getParam('id'))
                            ->toHtml(),
                ));

                $this->addTab('customers_tags', array(
                    'label'     => __('Customers Tagged Product'),
                    'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_tag_customer', 'admin.product.tags.customers')
                            ->setProductId($this->getRequest()->getParam('id'))
                            ->toHtml(),
                ));
            }

            if ($product->isBundle()) {

            	$this->addTab('bundle', array(
            		'label' => __('Bundle'),
            		'content' => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_bundle')->toHtml(),
            	));
            }

            if ($product->isSuperGroup()) {
            	$this->addTab('super', array(
            		'label' => __('Associated Products'),
            		'content' => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_group', 'admin.super.group.product')->toHtml()
            	));
            }
            elseif ($product->isSuperConfig()) {
            	$this->addTab('super', array(
            		'label' => __('Associated Products'),
            		'content' => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_config', 'admin.super.config.product')->toHtml()
            	));
            }
        }
        elseif ($setId) {
        	$this->addTab('super_settings', array(
                'label'     => __('Super Product Settings'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_settings')->toHtml(),
                'active'    => true
            ));
        }
        else {
            $this->addTab('set', array(
                'label'     => __('Settings'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_settings')->toHtml(),
                'active'    => true
            ));
        }
    }
}
