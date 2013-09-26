<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form Types Grid Block
 */
class Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Formtype_Grid
    extends Magento_Backend_Block_Widget_Grid_Extended
{
    /**
     * @var Magento_Eav_Model_Resource_Form_Type_CollectionFactory
     */
    protected $_formTypesFactory;

    /**
     * @var Magento_Core_Model_Theme_LabelFactory
     */
    protected $_themeLabelFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Eav_Model_Resource_Form_Type_CollectionFactory $formTypesFactory
     * @param Magento_Core_Model_Theme_LabelFactory $themeLabelFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Eav_Model_Resource_Form_Type_CollectionFactory $formTypesFactory,
        Magento_Core_Model_Theme_LabelFactory $themeLabelFactory,
        array $data = array()
    ) {
        $this->_formTypesFactory = $formTypesFactory;
        $this->_themeLabelFactory = $themeLabelFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Initialize Grid Block
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('code');
        $this->setDefaultDir('asc');
    }

    /**
     * Prepare grid collection object
     *
     * @return Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Formtype_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Magento_Eav_Model_Resource_Form_Type_Collection */
        $collection = $this->_formTypesFactory->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Formtype_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('code', array(
            'header'    => __('Type Code'),
            'index'     => 'code',
        ));

        $this->addColumn('label', array(
            'header'    => __('Label'),
            'index'     => 'label',
        ));

        $this->addColumn('store_id', array(
            'header'    => __('Store View'),
            'index'     => 'store_id',
            'type'      => 'store'
        ));

        /** @var $label Magento_Core_Model_Theme_Label */
        $label = $this->_themeLabelFactory->create();
        $design = $label->getLabelsCollection();
        array_unshift($design, array(
            'value' => 'all',
            'label' => __('All Themes')
        ));
        $this->addColumn('theme', array(
            'header'     => __('Theme'),
            'type'       => 'theme',
            'index'      => 'theme',
            'options'    => $design,
            'with_empty' => true,
            'default'    => __('All Themes')
        ));

        $this->addColumn('is_system', array(
            'header'    => __('System'),
            'index'     => 'is_system',
            'type'      => 'options',
            'options'   => array(
                0 => __('No'),
                1 => __('Yes'),
            )
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve row click URL
     *
     * @param Magento_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('type_id' => $row->getId()));
    }
}
