<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Items Tab in Edit RMA form
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Rma_New_Tab_Items extends Magento_Backend_Block_Widget_Form
    implements Magento_Backend_Block_Widget_Tab_Interface
{
    /**
     * Rma eav
     *
     * @var Magento_Rma_Helper_Eav
     */
    protected $_rmaEav = null;

    /**
     * @param Magento_Rma_Helper_Eav $rmaEav
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Rma_Helper_Eav $rmaEav,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_rmaEav = $rmaEav;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Class constructor
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('rma_items_grid');
    }

    /**
     * Get "Add Products" button
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        $addButtonData = array(
            'label' => __('Add Products'),
            'onclick' => "rma.addProduct()",
            'class' => 'add',
        );
        return $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')->setData($addButtonData)->toHtml();
    }

    /**
     * Get "Add products to RMA" button
     *
     * @return string
     */
    public function getAddProductButtonHtml()
    {
        $addButtonData = array(
            'label' => __('Add Selected Product(s) to returns'),
            'onclick' => "rma.addSelectedProduct()",
            'class' => 'add',
        );
        return $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')->setData($addButtonData)->toHtml();
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_New_Tab_Items
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form();
        $htmlIdPrefix = 'rma_properties_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $model = Mage::registry('current_rma');

        $fieldset = $form->addFieldset('rma_item_fields', array());

        $fieldset->addField('product_name', 'text', array(
            'label'=> __('Product Name'),
            'name' => 'product_name',
            'required'  => false
        ));

        $fieldset->addField('product_sku', 'text', array(
            'label'=> __('SKU'),
            'name' => 'product_sku',
            'required'  => false
        ));

        //Renderer puts available quantity instead of order_item_id
        $fieldset->addField('qty_ordered', 'text', array(
            'label'=> __('Remaining Qty'),
            'name' => 'qty_ordered',
            'required'  => false,
        ));

        $fieldset->addField('qty_requested', 'text', array(
            'label'=> __('Requested Qty'),
            'name' => 'qty_requested',
            'required' => false,
            'class' => 'validate-greater-than-zero'
        ));

        $reasonOtherAttribute =
            Mage::getModel('Magento_Rma_Model_Item_Form')->setFormCode('default')->getAttribute('reason_other');

        $fieldset->addField('reason_other', 'text', array(
            'label'     => $reasonOtherAttribute->getStoreLabel(),
            'name'      => 'reason_other',
            'maxlength' => 255,
            'required'  => false
        ));

        $eavHelper = $this->_rmaEav;
        $fieldset->addField('reason', 'select', array(
            'label'=> __('Reason to Return'),
            'options' => array(''=>'')
                + $eavHelper->getAttributeOptionValues('reason')
                + array('other' => $reasonOtherAttribute->getStoreLabel()),
            'name' => 'reason',
            'required' => false
        ))->setRenderer(
            $this->getLayout()->createBlock('Magento_Rma_Block_Adminhtml_Rma_New_Tab_Items_Renderer_Reason')
        );

        $fieldset->addField('condition', 'select', array(
            'label'=> __('Item Condition'),
            'options' => array(''=>'') + $eavHelper->getAttributeOptionValues('condition'),
            'name' => 'condition',
            'required' => false,
            'class' => 'action-select'
        ));

        $fieldset->addField('resolution', 'select', array(
            'label'=> __('Resolution'),
            'options' => array(''=>'') + $eavHelper->getAttributeOptionValues('resolution'),
            'name' => 'resolution',
            'required' => false,
            'class' => 'action-select'
        ));

        $fieldset->addField('delete_link', 'label', array(
            'label'=> __('Delete'),
            'name' => 'delete_link',
            'required' => false
        ));

        $fieldset->addField('add_details_link', 'label', array(
            'label'=> __('Add Details'),
            'name' => 'add_details_link',
            'required' => false
        ));

        $this->setForm($form);

        return $this;
    }

    /**
     * Get Header Text for Order Selection
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Items');
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Return Items');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}
