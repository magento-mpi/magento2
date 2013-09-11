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
 * Enterprise banner model
 *
 * @method \Magento\Banner\Model\Resource\Banner _getResource()
 * @method \Magento\Banner\Model\Resource\Banner getResource()
 * @method string getName()
 * @method \Magento\Banner\Model\Banner setName(string $value)
 * @method int getIsEnabled()
 * @method \Magento\Banner\Model\Banner setIsEnabled(int $value)
 * @method \Magento\Banner\Model\Banner setTypes(string $value)
 *
 * @category    Magento
 * @package     Magento_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Banner\Model;

class Banner extends \Magento\Core\Model\AbstractModel
{
    /**
     * Representation value of enabled banner
     *
     */
    const STATUS_ENABLED = 1;

    /**
     * Representation value of disabled banner
     *
     */
    const STATUS_DISABLED  = 0;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magento_banner';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getBanner() in this case
     *
     * @var string
     */
    protected $_eventObject = 'banner';

    /**
     * Store banner contents per store view
     *
     * @var array
     */
    protected $_contents = array();

    /**
     * Initialize banner model
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Banner\Model\Resource\Banner');
    }

    /**
     * Retrieve array of sales rules id's for banner
     *
     * @return array
     */
    public function getRelatedSalesRule()
    {
        if (!$this->getId()) {
            return array();
        }
        $array = $this->getData('related_sales_rule');
        if (is_null($array)) {
            $array = $this->getResource()->getRelatedSalesRule($this->getId());
            $this->setData('related_sales_rule', $array);
        }
        return $array;
    }

    /**
     * Retrieve array of catalog rules id's for banner
     *
     * @return array
     */
    public function getRelatedCatalogRule()
    {
        if (!$this->getId()) {
            return array();
        }
        $array = $this->getData('related_catalog_rule');
        if (is_null($array)) {
            $array = $this->getResource()->getRelatedCatalogRule($this->getId());
            $this->setData('related_catalog_rule', $array);
        }
        return $array;
    }

    /**
     * Get all existing banner contents
     *
     * @return array
     */
    public function getStoreContents()
    {
        if (!$this->hasStoreContents()) {
            $contents = $this->_getResource()->getStoreContents($this->getId());
            $this->setStoreContents($contents);
        }
        return $this->_getData('store_contents');
    }

    /**
     * Get banners ids by catalog rule id
     *
     * @param int $ruleId
     * @return array
     */
    public function getRelatedBannersByCatalogRuleId($ruleId)
    {
        if (!$this->hasRelatedCatalogRuleBanners()) {
            $banners = $this->_getResource()->getRelatedBannersByCatalogRuleId($ruleId);
            $this->setRelatedCatalogRuleBanners($banners);
        }
        return $this->_getData('related_catalog_rule_banners');
    }

    /**
     * Get banners ids by sales rule id
     *
     * @param int $ruleId
     * @return array
     */
    public function getRelatedBannersBySalesRuleId($ruleId)
    {
        if (!$this->hasRelatedSalesRuleBanners()) {
            $banners = $this->_getResource()->getRelatedBannersBySalesRuleId($ruleId);
            $this->setRelatedSalesRuleBanners($banners);
        }
        return $this->_getData('related_sales_rule_banners');
    }

    /**
     * Save banner content, bind banner to catalog and sales rules after banner save
     *
     * @return \Magento\Banner\Model\Banner
     */
    protected function _afterSave()
    {
        if ($this->hasStoreContents()) {
            $this->_getResource()
                ->saveStoreContents($this->getId(), $this->getStoreContents(), $this->getStoreContentsNotUse());
        }
        if ($this->hasBannerCatalogRules()) {
            $this->_getResource()->saveCatalogRules(
                $this->getId(),
                $this->getBannerCatalogRules()
            );
        }
        if ($this->hasBannerSalesRules()) {
            $this->_getResource()->saveSalesRules(
                $this->getId(),
                $this->getBannerSalesRules()
            );
        }
        return parent::_afterSave();
    }

    /**
     * Validate some data before saving
     * @return \Magento\Banner\Model\Banner
     */
    protected function _beforeSave()
    {
        if ('' == trim($this->getName())) {
            \Mage::throwException(__('Please enter a name.'));
        }
        $bannerContents = $this->getStoreContents();
        $flag = false;
        foreach ($bannerContents as $content) {
            if ('' != trim($content)) {
                $flag = true;
                break;
            }
        }
        if (!$flag) {
            // @codingStandardsIgnoreStart
            \Mage::throwException(__('Please specify default content for at least one store view.'));
            // @codingStandardsIgnoreEnd
        }
        return parent::_beforeSave();
    }

    /**
     * Collect store ids in which current banner has content
     *
     * @return array
     */
    public function getStoreIds()
    {
        $contents = $this->getStoreContents();
        if (!$this->hasStoreIds()) {
            $this->setStoreIds(array_keys($contents));
        }
        return $this->_getData('store_ids');
    }

    /**
     * Make types getter always return array
     * @return array
     */
    public function getTypes()
    {
        $types = $this->_getData('types');
        if (is_array($types)) {
            return $types;
        }
        if (empty($types)) {
            $types = array();
        } else {
            $types = explode(',', $types);
        }
        $this->setData('types', $types);
        return $types;
    }
}
