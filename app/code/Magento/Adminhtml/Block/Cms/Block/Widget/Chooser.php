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
 * CMS block chooser for Wysiwyg CMS widget
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Cms_Block_Widget_Chooser extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Magento_Cms_Model_BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var Magento_Cms_Model_Resource_Block_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Cms_Model_BlockFactory $blockFactory
     * @param Magento_Cms_Model_Resource_Block_CollectionFactory $collectionFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Cms_Model_BlockFactory $blockFactory,
        Magento_Cms_Model_Resource_Block_CollectionFactory $collectionFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_blockFactory = $blockFactory;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Block construction, prepare grid params
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('block_identifier');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setDefaultFilter(array('chooser_is_active' => '1'));
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Magento_Data_Form_Element_Abstract $element Form Element
     * @return Magento_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $uniqId = $this->_coreData->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('*/cms_block_widget/chooser', array('uniq_id' => $uniqId));

        $chooser = $this->getLayout()->createBlock('Magento_Widget_Block_Adminhtml_Widget_Chooser')
            ->setElement($element)
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);


        if ($element->getValue()) {
            $block = $this->_blockFactory->create()->load($element->getValue());
            if ($block->getId()) {
                $chooser->setLabel($block->getTitle());
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var blockId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                var blockTitle = trElement.down("td").next().innerHTML;
                '.$chooserJsObject.'.setElementValue(blockId);
                '.$chooserJsObject.'.setElementLabel(blockTitle);
                '.$chooserJsObject.'.close();
            }
        ';
        return $js;
    }

    /**
     * Prepare Cms static blocks collection
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->_collectionFactory->create());
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for Cms blocks grid
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('chooser_id', array(
            'header'    => __('ID'),
            'align'     => 'right',
            'index'     => 'block_id',
            'width'     => 50
        ));

        $this->addColumn('chooser_title', array(
            'header'    => __('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('chooser_identifier', array(
            'header'    => __('Identifier'),
            'align'     => 'left',
            'index'     => 'identifier'
        ));


        $this->addColumn('chooser_is_active', array(
            'header'    => __('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                0 => __('Disabled'),
                1 => __('Enabled')
            ),
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/cms_block_widget/chooser', array('_current' => true));
    }
}
