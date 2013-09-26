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
namespace Magento\Rma\Block\Adminhtml\Rma\NewRma\Tab;

class Items extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Rma eav
     *
     * @var \Magento\Rma\Helper\Eav
     */
    protected $_rmaEav;
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Rma\Model\Item\FormFactory
     */
    protected $_itemFormFactory;

    /**
     * @param \Magento\Rma\Helper\Eav $rmaEav
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Rma\Model\Item\FormFactory $itemFormFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rma\Helper\Eav $rmaEav,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Rma\Model\Item\FormFactory $itemFormFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_rmaEav = $rmaEav;
        $this->_itemFormFactory = $itemFormFactory;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
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
        return $this->getLayout()
            ->createBlock('Magento\Adminhtml\Block\Widget\Button')
            ->setData($addButtonData)
            ->toHtml();
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
        return $this->getLayout()
            ->createBlock('Magento\Adminhtml\Block\Widget\Button')
            ->setData($addButtonData)
            ->toHtml();
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\Rma\Block\Adminhtml\Rma\NewRma\Tab\Items
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $htmlIdPrefix = 'rma_properties_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $model = $this->_coreRegistry->registry('current_rma');

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

        /** @var $itemForm \Magento\Rma\Model\Item\Form */
        $itemForm = $this->_itemFormFactory->create();
        $reasonOtherAttribute = $itemForm->setFormCode('default')->getAttribute('reason_other');

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
            $this->getLayout()->createBlock('Magento\Rma\Block\Adminhtml\Rma\NewRma\Tab\Items\Renderer\Reason')
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
