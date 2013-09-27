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
 * CMS page chooser for Wysiwyg CMS widget
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Cms_Page_Widget_Chooser extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Magento_Page_Model_Source_Layout
     */
    protected $_pageLayout;

    /**
     * @var Magento_Cms_Model_Page
     */
    protected $_cmsPage;

    /**
     * @var Magento_Cms_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * @var Magento_Cms_Model_Resource_Page_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Page_Model_Source_Layout $pageLayout
     * @param Magento_Cms_Model_Page $cmsPage
     * @param Magento_Cms_Model_PageFactory $pageFactory
     * @param Magento_Cms_Model_Resource_Page_CollectionFactory $collectionFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Page_Model_Source_Layout $pageLayout,
        Magento_Cms_Model_Page $cmsPage,
        Magento_Cms_Model_PageFactory $pageFactory,
        Magento_Cms_Model_Resource_Page_CollectionFactory $collectionFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_pageLayout = $pageLayout;
        $this->_cmsPage = $cmsPage;
        $this->_pageFactory = $pageFactory;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Block construction, prepare grid params
     */
    protected function _construct()
    {
        parent::_construct();
        //$this->setDefaultSort('name');
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
        $sourceUrl = $this->getUrl('*/cms_page_widget/chooser', array('uniq_id' => $uniqId));

        $chooser = $this->getLayout()->createBlock('Magento_Widget_Block_Adminhtml_Widget_Chooser')
            ->setElement($element)
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);


        if ($element->getValue()) {
            $page = $this->_pageFactory->create()->load((int)$element->getValue());
            if ($page->getId()) {
                $chooser->setLabel($page->getTitle());
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
                var pageTitle = trElement.down("td").next().innerHTML;
                var pageId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                '.$chooserJsObject.'.setElementValue(pageId);
                '.$chooserJsObject.'.setElementLabel(pageTitle);
                '.$chooserJsObject.'.close();
            }
        ';
        return $js;
    }

    /**
     * Prepare pages collection
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        /* @var $collection Magento_Cms_Model_Resource_Page_Collection */
        $collection->setFirstStoreFlag(true);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for pages grid
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('chooser_id', array(
            'header'    => __('ID'),
            'index'     => 'page_id',
            'header_css_class'  => 'col-id',
            'column_css_class'  => 'col-id'
        ));

        $this->addColumn('chooser_title', array(
            'header'    => __('Title'),
            'index'     => 'title',
            'header_css_class'  => 'col-title',
            'column_css_class'  => 'col-title'
        ));

        $this->addColumn('chooser_identifier', array(
            'header'    => __('URL Key'),
            'index'     => 'identifier',
            'header_css_class'  => 'col-url',
            'column_css_class'  => 'col-url'
        ));

        $this->addColumn('chooser_root_template', array(
            'header'    => __('Layout'),
            'index'     => 'root_template',
            'type'      => 'options',
            'options'   => $this->_pageLayout->getOptions(),
            'header_css_class'  => 'col-layout',
            'column_css_class'  => 'col-layout'
        ));

        $this->addColumn('chooser_is_active', array(
            'header'    => __('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => $this->_cmsPage->getAvailableStatuses(),
            'header_css_class'  => 'col-status',
            'column_css_class'  => 'col-status'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/cms_page_widget/chooser', array('_current' => true));
    }
}
