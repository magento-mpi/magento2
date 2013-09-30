<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Banner_Block_Adminhtml_Promo_Salesrule_Edit_Tab_Banners_Grid
    extends Magento_Banner_Block_Adminhtml_Banner_Grid
{
    /**
     * @var Magento_Core_Model_Registry
     */
    protected $_registry;

    /**
     * @var Magento_Banner_Model_BannerFactory
     */
    protected $_bannerFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Banner_Model_Resource_Banner_CollectionFactory $bannerColFactory
     * @param Magento_Banner_Model_Config $bannerConfig
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Banner_Model_BannerFactory $bannerFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Banner_Model_Resource_Banner_CollectionFactory $bannerColFactory,
        Magento_Banner_Model_Config $bannerConfig,
        Magento_Core_Model_Registry $registry,
        Magento_Banner_Model_BannerFactory $bannerFactory,
        array $data = array()
    ) {
        $this->_registry = $registry;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $bannerColFactory, $bannerConfig, $data);
        $this->_bannerFactory = $bannerFactory;
    }

    /**
     * Initialize grid, set promo sales rule grid ID
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('related_salesrule_banners_grid');
        $this->setVarNameFilter('related_salesrule_banners_filter');
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
        return $this->getUrl('*/banner/salesRuleBannersGrid', array('_current' => true));
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
        $banners = $this->getSelectedSalesruleBanners();
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
        return $this->_bannerFactory->create()->getRelatedBannersBySalesRuleId($this->_getRule()->getRuleId());
    }

    /**
     * Get current sales rule model
     *
     * @return Magento_SalesRule_Model_Rule
     */
    protected function _getRule()
    {
        return $this->_registry->registry('current_promo_quote_rule');
    }
}
