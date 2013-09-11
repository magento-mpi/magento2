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
namespace Magento\Adminhtml\Block\Cms\Page\Widget;

class Chooser extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments Object data
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
     * @param \Magento\Data\Form\Element\AbstractElement $element Form Element
     * @return \Magento\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $uniqId = \Mage::helper('Magento\Core\Helper\Data')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('*/cms_page_widget/chooser', array('uniq_id' => $uniqId));

        $chooser = $this->getLayout()->createBlock('Magento\Widget\Block\Adminhtml\Widget\Chooser')
            ->setElement($element)
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);


        if ($element->getValue()) {
            $page = \Mage::getModel('Magento\Cms\Model\Page')->load((int)$element->getValue());
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
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = \Mage::getModel('Magento\Cms\Model\Page')->getCollection();
        /* @var $collection \Magento\Cms\Model\Resource\Page\Collection */
        $collection->setFirstStoreFlag(true);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for pages grid
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
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
            'options'   => \Mage::getSingleton('Magento\Page\Model\Source\Layout')->getOptions(),
            'header_css_class'  => 'col-layout',
            'column_css_class'  => 'col-layout'
        ));

        $this->addColumn('chooser_is_active', array(
            'header'    => __('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => \Mage::getModel('Magento\Cms\Model\Page')->getAvailableStatuses(),
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
