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
 * Customer edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit extends Mage_Adminhtml_Block_Widget
{
    protected $_template = 'catalog/product/edit.phtml';

    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_edit');
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    protected function _prepareLayout()
    {
        if (!$this->getRequest()->getParam('popup')) {
            $this->setChild('back_button',
                $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                    ->setData(array(
                        'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Back'),
                        'onclick'   => 'setLocation(\''
                            . $this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
                        'class' => 'back'
                    ))
            );
        } else {
            $this->setChild('back_button',
                $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                    ->setData(array(
                        'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Close Window'),
                        'onclick'   => 'window.close()',
                        'class' => 'cancel'
                    ))
            );
        }

        if (!$this->getProduct()->isReadonly()) {
            $this->setChild('reset_button',
                $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                    ->setData(array(
                        'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Reset'),
                        'onclick'   => 'setLocation(\''.$this->getUrl('*/*/*', array('_current'=>true)).'\')'
                    ))
            );

            $this->setChild('save_button',
                $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                    ->setData(array(
                        'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Save'),
                        'onclick'   => 'productForm.submit()',
                        'class' => 'save'
                    ))
            );
        }

        if (!$this->getRequest()->getParam('popup')) {
            if (!$this->getProduct()->isReadonly()) {
                $this->setChild('save_and_edit_button',
                    $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                        ->setData(array(
                            'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Save and Continue Edit'),
                            'onclick'   => 'saveAndContinueEdit(\''.$this->getSaveAndContinueUrl().'\')',
                            'class' => 'save'
                        ))
                );
            }
            if ($this->getProduct()->isDeleteable()) {
                $this->setChild('delete_button',
                $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                        ->setData(array(
                            'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Delete'),
                            'onclick'   => 'confirmSetLocation(\''
                                . Mage::helper('Mage_Catalog_Helper_Data')->__('Are you sure?').'\', \''.$this->getDeleteUrl().'\')',
                            'class'  => 'delete'
                        ))
                );
            }

            if ($this->getProduct()->isDuplicable()) {
                $this->setChild('duplicate_button',
                $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                    ->setData(array(
                        'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Duplicate'),
                        'onclick'   => 'setLocation(\'' . $this->getDuplicateUrl() . '\')',
                        'class'  => 'add'
                    ))
                );
            }
        }

        return parent::_prepareLayout();
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getCancelButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getSaveAndEditButtonHtml()
    {
        return $this->getChildHtml('save_and_edit_button');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getDuplicateButtonHtml()
    {
        return $this->getChildHtml('duplicate_button');
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }

    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'tab'        => '{{tab_id}}',
            'active_tab' => null
        ));
    }

    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    public function getProductSetId()
    {
        $setId = false;
        if (!($setId = $this->getProduct()->getAttributeSetId()) && $this->getRequest()) {
            $setId = $this->getRequest()->getParam('set', null);
        }
        return $setId;
    }

    public function getIsGrouped()
    {
        return $this->getProduct()->isGrouped();
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }

    public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', array('_current'=>true));
    }

    public function getHeader()
    {
        $header = '';
        if ($this->getProduct()->getId()) {
            $header = $this->escapeHtml($this->getProduct()->getName());
        }
        else {
            $header = Mage::helper('Mage_Catalog_Helper_Data')->__('New Product');
        }
        if ($setName = $this->getAttributeSetName()) {
            $header.= ' (' . $setName . ')';
        }
        return $header;
    }

    public function getAttributeSetName()
    {
        if ($setId = $this->getProduct()->getAttributeSetId()) {
            $set = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set')
                ->load($setId);
            return $set->getAttributeSetName();
        }
        return '';
    }

    public function getIsConfigured()
    {
        $result = true;

        $product = $this->getProduct();
        if ($product->isConfigurable()) {
            $superAttributes = $product->getTypeInstance()->getUsedProductAttributeIds($product);
            $result = !empty($superAttributes);
        }

        return $result;
    }

    public function getSelectedTabId()
    {
        return addslashes(htmlspecialchars($this->getRequest()->getParam('tab')));
    }
}
