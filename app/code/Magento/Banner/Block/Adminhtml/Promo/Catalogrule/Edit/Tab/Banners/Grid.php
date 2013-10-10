<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Block\Adminhtml\Promo\Catalogrule\Edit\Tab\Banners;

class Grid
    extends \Magento\Banner\Block\Adminhtml\Banner\Grid
{
    /**
     * Banner model
     *
     * @var \Magento\Banner\Model\BannerFactory
     */

    protected $_bannerFactory = null;
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Banner\Model\Resource\Banner\CollectionFactory $bannerColFactory
     * @param \Magento\Banner\Model\Config $bannerConfig
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Banner\Model\BannerFactory $bannerFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Banner\Model\Resource\Banner\CollectionFactory $bannerColFactory,
        \Magento\Banner\Model\Config $bannerConfig,
        \Magento\Core\Model\Registry $registry,
        \Magento\Banner\Model\BannerFactory $bannerFactory,
        array $data = array()
    ) {
        $this->_registry = $registry;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $bannerColFactory, $bannerConfig, $data);
        $this->_bannerFactory = $bannerFactory;
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
     * @return \Magento\Adminhtml\Block\Widget\Grid
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
     * @return \Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Promotions\Salesrule
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
     * @return \Magento\Banner\Block\Adminhtml\Promo\Salesrule\Edit\Tab\Banners\Grid
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
        $ruleId = $this->_registry->registry('current_promo_catalog_rule')->getRuleId();
        return $this->_bannerFactory->create()->getRelatedBannersByCatalogRuleId($ruleId);
    }

    /**
     * Get current catalog rule model
     *
     * @return \Magento\CatalogRule\Model\Rule
     */
    protected function _getRule()
    {
        return $this->_registry->registry('current_promo_catalog_rule');
    }
}
