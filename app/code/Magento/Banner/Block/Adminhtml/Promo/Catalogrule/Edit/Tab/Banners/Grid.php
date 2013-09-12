<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Banner_Block_Adminhtml_Promo_Catalogrule_Edit_Tab_Banners_Grid
    extends Magento_Banner_Block_Adminhtml_Banner_Grid
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $storeManager, $urlModel, $data);
    }

    /**
     * Initialize grid, set promo catalog rule grid ID
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('related_catalogrule_banners_grid');
        $this->setVarNameFilter('related_catalogrule_banners_filter');
        if ($this->_getRule() && $this->_getRule()->getId()) {
            $this->setDefaultFilter(array('in_banners' => 1));
        }
    }

    /**
     * Create grid columns
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_banners', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_banners',
            'values'    => $this->_getSelectedBanners(),
            'align'     => 'center',
            'index'     => 'banner_id'
        ));
        parent::_prepareColumns();
    }

    /* Set custom filter for in banner flag
     *
     * @param string $column
     * @return Magento_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_Salesrule
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_banners') {
            $bannerIds = $this->_getSelectedBanners();
            if (empty($bannerIds)) {
                $bannerIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.banner_id', array('in' => $bannerIds));
            } else {
                if ($bannerIds) {
                    $this->getCollection()->addFieldToFilter('main_table.banner_id', array('nin' => $bannerIds));
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
     * @return Magento_Banner_Block_Adminhtml_Promo_Salesrule_Edit_Tab_Banners_Grid
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Ajax grid URL getter
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/banner/catalogRuleBannersGrid', array('_current' => true));
    }

    /**
     * Define row click callback
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }

    /**
     * Get selected banners ids for in banner flag
     *
     * @return array
     */
    protected function _getSelectedBanners()
    {
        $banners = $this->getSelectedCatalogruleBanners();
        if (is_null($banners)) {
            $banners = $this->getRelatedBannersByRule();
        }
        return $banners;
    }

    /**
     * Get related banners by current rule
     *
     * @return array
     */
    public function getRelatedBannersByRule()
    {
        $ruleId = $this->_coreRegistry->registry('current_promo_catalog_rule')->getRuleId();
        return Mage::getModel('Magento_Banner_Model_Banner')->getRelatedBannersByCatalogRuleId($ruleId);
    }

    /**
     * Get current catalog rule model
     *
     * @return Magento_CatalogRule_Model_Rule
     */
    protected function _getRule()
    {
        return $this->_coreRegistry->registry('current_promo_catalog_rule');
    }
}
