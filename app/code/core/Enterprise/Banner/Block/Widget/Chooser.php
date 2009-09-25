<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Banner
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Banners chooser for Banner Rotator widget
 *
 * @category   Enterprise
 * @package    Enterprise_Banner
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Banner_Block_Widget_Chooser extends Enterprise_Banner_Block_Adminhtml_Banner_Grid
{
    protected $_selectedBanners = array();

    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments Object data
     */
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $this->setVarNameFilter('banners_filter');
        $this->setDefaultSort('banner_id');
        $this->setUseAjax(true);
        $this->setDefaultFilter(array('in_banners'=>1));
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = $element->getId() . md5(microtime());
        $sourceUrl = $this->getUrl('*/banner_widget/chooser', array(
            'uniq_id' => $uniqId
        ));

        $chooser = $this->getLayout()->createBlock('adminhtml/cms_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        if ($element->getValue()) {
            $chooser->setLabel($element->getValue());
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Grid row init js callback
     *
     * @return string
     */
    public function getRowInitCallback()
    {
        $chooserJsObject = $this->getId();
        return '
        function(grid, row){
            if(typeof(grid.selBannersIds) == \'undefined\'){
                grid.selBannersIds = [];
                if('.$chooserJsObject.'.getElementValue() != \'\'){
                    grid.selBannersIds = '.$chooserJsObject.'.getElementValue().split(\',\');
                }
                grid.reloadParams = {};
                grid.reloadParams[\'selected_banners[]\'] = grid.selBannersIds;
            }
            var inputs      = Element.select($(row), \'input\');
            var checkbox    = inputs[0];
            var position    = inputs[1];
            var indexOf = grid.selBannersIds.indexOf(checkbox.value);
            if(indexOf >= 0){
                checkbox.checked = true;
                position.value = indexOf+1;
            }
        }
        ';
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        return '
            function (grid, event) {
                if(typeof(grid.selBannersIds) == \'undefined\'){
                    grid.selBannersIds = [];
                }

                var trElement   = Event.findElement(event, "tr");
                var isInput     = Event.element(event).tagName == \'INPUT\';
                var inputs      = Element.select(trElement, \'input\');
                var checkbox    = inputs[0];
                var position    = inputs[1].value;
                var bannersNum  = grid.selBannersIds.length;
                var checked     = isInput ? checkbox.checked : !checkbox.checked;
                checkbox.checked = checked;

                var bannerName  = trElement.down("td").next().next().innerHTML;
                var bannerId    = checkbox.value;

                if(checked){
                    if(grid.selBannersIds.indexOf(bannerId) < 0){
                        if(position-1 >= bannersNum || bannersNum == 0){
                            grid.selBannersIds.push(bannerId);
                        }
                        else if(position == \'0\' || position == \'\'){
                            grid.selBannersIds.splice(0, 0, bannerId);
                        }
                        else{
                            grid.selBannersIds.splice(position-1, 0, bannerId);
                        }
                    }
                }
                else{
                    grid.selBannersIds = grid.selBannersIds.without(bannerId);
                }
                '.$chooserJsObject.'.setElementValue(grid.selBannersIds.join(\',\'));
                '.$chooserJsObject.'.setElementLabel(grid.selBannersIds.join(\',\'));
                grid.reloadParams = {};
                grid.reloadParams[\'selected_banners[]\'] = grid.selBannersIds;
            }
        ';
    }

    /**
     * Create grid columns
     *
     * @return Enterprise_Banner_Block_Widget_Chooser
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_banners', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_banners',
            'values'    => $this->getSelectedBanners(),
            'align'     => 'center',
            'index'     => 'banner_id'
        ));

        $this->addColumn('position', array(
            'header'            => Mage::helper('enterprise_banner')->__('Position'),
            'name'              => 'position',
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'position',
            'width'             => 60,
            'editable'          => true
        ));

        parent::_prepareColumns();

        return $this;
    }

    /* Set custom filter for in banner flag
     *
     * @param string $column
     * @return Enterprise_Banner_Block_Widget_Chooser
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_banners') {
            $bannerIds = $this->getSelectedBanners();
            if (empty($bannerIds)) {
                $bannerIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('banner_id', array('in'=>$bannerIds));
            }
            else {
                if($bannerIds) {
                    $this->getCollection()->addFieldToFilter('banner_id', array('nin'=>$bannerIds));
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Disable massaction functioanality
     *
     * @return Enterprise_Banner_Block_Widget_Chooser
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Adds additional parameter to URL for loading only banners grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/banner_widget/chooser', array(
            'banners_grid' => true,
            '_current' => true,
            'uniq_id' => $this->getId()
        ));
    }

    /**
     * Setter
     *
     * @param array $selectedBanners
     * @return Enterprise_Banner_Block_Widget_Chooser
     */
    public function setSelectedBanners($selectedBanners)
    {
        $this->_selectedBanners = $selectedBanners;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSelectedBanners()
    {
        $elementValue = $this->getRequest()->getParam('element_value', null);
        if ($elementValue) {
            $elementValue = explode(',', $elementValue);
        }
        if ($selectedBanners = $this->getRequest()->getParam('selected_banners', $elementValue)) {
            $this->setSelectedBanners($selectedBanners);
        }
        return $this->_selectedBanners;
    }
}
