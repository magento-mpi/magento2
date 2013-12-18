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
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype;

class Grid
    extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Eav\Model\Resource\Form\Type\CollectionFactory
     */
    protected $_formTypesFactory;

    /**
     * @var \Magento\View\Design\Theme\LabelFactory
     */
    protected $_themeLabelFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Eav\Model\Resource\Form\Type\CollectionFactory $formTypesFactory
     * @param \Magento\View\Design\Theme\LabelFactory $themeLabelFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Eav\Model\Resource\Form\Type\CollectionFactory $formTypesFactory,
        \Magento\View\Design\Theme\LabelFactory $themeLabelFactory,
        array $data = array()
    ) {
        $this->_formTypesFactory = $formTypesFactory;
        $this->_themeLabelFactory = $themeLabelFactory;
        parent::__construct($context, $urlModel, $backendHelper, $data);
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
     * @return \Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype\Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection \Magento\Eav\Model\Resource\Form\Type\Collection */
        $collection = $this->_formTypesFactory->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return \Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype\Grid
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

        /** @var $label \Magento\View\Design\Theme\Label */
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
     * @param \Magento\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/*/edit', array('type_id' => $row->getId()));
    }
}
