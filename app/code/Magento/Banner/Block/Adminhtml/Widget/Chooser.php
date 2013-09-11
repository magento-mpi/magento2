<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Banners chooser for Banner Rotator widget
 *
 * @category   Magento
 * @package    Magento_Banner
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Banner\Block\Adminhtml\Widget;

class Chooser extends \Magento\Banner\Block\Adminhtml\Banner\Grid
{
    /**
     * Store selected banner Ids
     * Used in initial setting selected banners
     *
     * @var array
     */
    protected $_selectedBanners = array();

    /**
     * Store hidden banner ids field id
     *
     * @var string
     */
    protected $_elementValueId = '';

    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments Object data
     */
    public function _construct()
    {
        parent::_construct();
        $this->setDefaultFilter(array('in_banners'=>1));
    }

    /**
     * Prepare chooser element HTML
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element Form Element
     * @return \Magento\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->_elementValueId = "{$element->getId()}";
        $this->_selectedBanners = explode(',', $element->getValue());

        //Create hidden field that store selected banner ids
        $hidden = new \Magento\Data\Form\Element\Hidden($element->getData());
        $hidden->setId($this->_elementValueId)->setForm($element->getForm());
        $hiddenHtml = $hidden->getElementHtml();

        $element->setValue('')->setValueClass('value2');
        $element->setData('after_element_html', $hiddenHtml . $this->toHtml());

        return $element;
    }

    /**
     * Grid row init js callback
     *
     * @return string
     */
    public function getRowInitCallback()
    {
        return '
        function(grid, row){
            if(!grid.selBannersIds){
                grid.selBannersIds = {};
                if($(\'' . $this->_elementValueId . '\').value != \'\'){
                    var elementValues = $(\'' . $this->_elementValueId . '\').value.split(\',\');
                    for(var i = 0; i < elementValues.length; i++){
                        grid.selBannersIds[elementValues[i]] = i+1;
                    }
                }
                grid.reloadParams = {};
                grid.reloadParams[\'selected_banners[]\'] = Object.keys(grid.selBannersIds);
            }
            var inputs      = Element.select($(row), \'input\');
            var checkbox    = inputs[0];
            var position    = inputs[1];
            var bannersNum  = grid.selBannersIds.length;
            var bannerId    = checkbox.value;

            inputs[1].checkboxElement = checkbox;

            var indexOf = Object.keys(grid.selBannersIds).indexOf(bannerId);
            if(indexOf >= 0){
                checkbox.checked = true;
                if (!position.value) {
                    position.value = indexOf + 1;
                }
            }

            Event.observe(position,\'change\', function(){
                var checkb = Element.select($(row), \'input\')[0];
                if(checkb.checked){
                    grid.selBannersIds[checkb.value] = this.value;
                    var idsclone = Object.clone(grid.selBannersIds);
                    var bans = Object.keys(grid.selBannersIds);
                    var pos = Object.values(grid.selBannersIds).sort(sortNumeric);
                    var banners = [];
                    var k = 0;

                    for(var j = 0; j < pos.length; j++){
                        for(var i = 0; i < bans.length; i++){
                            if(idsclone[bans[i]] == pos[j]){
                                banners[k] = bans[i];
                                k++;
                                delete(idsclone[bans[i]]);
                                break;
                            }
                        }
                    }
                    $(\'' . $this->_elementValueId . '\').value = banners.join(\',\');
                }
            });
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
        return '
            function (grid, event) {
                if(!grid.selBannersIds){
                    grid.selBannersIds = {};
                }

                var trElement   = Event.findElement(event, "tr");
                var isInput     = Event.element(event).tagName == \'INPUT\';
                var inputs      = Element.select(trElement, \'input\');
                var checkbox    = inputs[0];
                var position    = inputs[1].value || 1;
                var checked     = isInput ? checkbox.checked : !checkbox.checked;
                checkbox.checked = checked;
                var bannerId    = checkbox.value;

                if(checked){
                    if(Object.keys(grid.selBannersIds).indexOf(bannerId) < 0){
                        grid.selBannersIds[bannerId] = position;
                    }
                }
                else{
                    delete(grid.selBannersIds[bannerId]);
                }

                var idsclone = Object.clone(grid.selBannersIds);
                var bans = Object.keys(grid.selBannersIds);
                var pos = Object.values(grid.selBannersIds).sort(sortNumeric);
                var banners = [];
                var k = 0;
                for(var j = 0; j < pos.length; j++){
                    for(var i = 0; i < bans.length; i++){
                        if(idsclone[bans[i]] == pos[j]){
                            banners[k] = bans[i];
                            k++;
                            delete(idsclone[bans[i]]);
                            break;
                        }
                    }
                }
                $(\'' . $this->_elementValueId . '\').value = banners.join(\',\');
                grid.reloadParams = {};
                grid.reloadParams[\'selected_banners[]\'] = banners;
            }
        ';
    }

    /**
     * Checkbox Check JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback()
    {
        return 'function (grid, element, checked) {
                    if(!grid.selBannersIds){
                        grid.selBannersIds = {};
                    }
                    var checkbox    = element;

                    checkbox.checked = checked;
                    var bannerId    = checkbox.value;
                    if(bannerId == \'on\'){
                        return;
                    }
                    var trElement   = element.up(\'tr\');
                    var inputs      = Element.select(trElement, \'input\');
                    var position    = inputs[1].value || 1;

                    if(checked){
                        if(Object.keys(grid.selBannersIds).indexOf(bannerId) < 0){
                            grid.selBannersIds[bannerId] = position;
                        }
                    }
                    else{
                        delete(grid.selBannersIds[bannerId]);
                    }

                    var idsclone = Object.clone(grid.selBannersIds);
                    var bans = Object.keys(grid.selBannersIds);
                    var pos = Object.values(grid.selBannersIds).sort(sortNumeric);
                    var banners = [];
                    var k = 0;
                    for(var j = 0; j < pos.length; j++){
                        for(var i = 0; i < bans.length; i++){
                            if(idsclone[bans[i]] == pos[j]){
                                banners[k] = bans[i];
                                k++;
                                delete(idsclone[bans[i]]);
                                break;
                            }
                        }
                    }
                    $(\'' . $this->_elementValueId . '\').value = banners.join(\',\');
                    grid.reloadParams = {};
                    grid.reloadParams[\'selected_banners[]\'] = banners;
                }';
    }

    /**
     * Create grid columns
     *
     * @return Magento_Banner_Block_Widget_Chooser
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_banners', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_banners',
            'values'    => $this->getSelectedBanners(),
            'align'     => 'center',
            'index'     => 'banner_id',
        ));

        $this->addColumn('position', array(
            'header'         => __('Position'),
            'name'           => 'position',
            'type'           => 'number',
            'validate_class' => 'validate-number',
            'index'          => 'position',
            'editable'       => true,
            'filter'         => false,
            'edit_only'      => true,
            'sortable'       => false
        ));
        $this->addColumnsOrder('position', 'banner_is_enabled');

        return parent::_prepareColumns();
    }

    /* Set custom filter for in banner flag
     *
     * @param string $column
     * @return Magento_Banner_Block_Widget_Chooser
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_banners') {
            $bannerIds = $this->getSelectedBanners();
            if (empty($bannerIds)) {
                $bannerIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addBannerIdsFilter($bannerIds);
            } else {
                if ($bannerIds) {
                    $this->getCollection()->addBannerIdsFilter($bannerIds, true);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Disable massaction functioanality
     *
     * @return Magento_Banner_Block_Widget_Chooser
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
            'uniq_id' => $this->getId(),
            'selected_banners' => join(',', $this->getSelectedBanners())
        ));
    }

    /**
     * Setter
     *
     * @param array $selectedBanners
     * @return Magento_Banner_Block_Widget_Chooser
     */
    public function setSelectedBanners($selectedBanners)
    {
        if (is_string($selectedBanners)) {
            $selectedBanners = explode(',', $selectedBanners);
        }        
        $this->_selectedBanners = $selectedBanners;
        return $this;
    }

    /**
     * Set banners' positions of saved banners
     *
     * @return \Magento\Banner\Block\Adminhtml\Widget\Chooser
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        
        foreach ($this->getCollection() as $item) {
            foreach ($this->getSelectedBanners() as $pos => $banner) {
                if ($banner == $item->getBannerId()) {
                    $item->setPosition($pos + 1);
                }
            }
        }
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSelectedBanners()
    {
        if ($selectedBanners = $this->getRequest()->getParam('selected_banners', $this->_selectedBanners)) {
            $this->setSelectedBanners($selectedBanners);
        }
        return $this->_selectedBanners;
    }
}
