<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Entity;

use Magento\Catalog\Model\Attribute\LockValidatorInterface;

/**
 * Product attribute extension with event dispatching
 *
 * @method \Magento\Catalog\Model\Resource\Attribute _getResource()
 * @method \Magento\Catalog\Model\Resource\Attribute getResource()
 * @method string getFrontendInputRenderer()
 * @method \Magento\Catalog\Model\Entity\Attribute setFrontendInputRenderer(string $value)
 * @method int setIsGlobal(int $value)
 * @method int getIsVisible()
 * @method int setIsVisible(int $value)
 * @method int getIsSearchable()
 * @method \Magento\Catalog\Model\Entity\Attribute setIsSearchable(int $value)
 * @method int getSearchWeight()
 * @method \Magento\Catalog\Model\Entity\Attribute setSearchWeight(int $value)
 * @method int getIsFilterable()
 * @method \Magento\Catalog\Model\Entity\Attribute setIsFilterable(int $value)
 * @method int getIsComparable()
 * @method \Magento\Catalog\Model\Entity\Attribute setIsComparable(int $value)
 * @method \Magento\Catalog\Model\Entity\Attribute setIsVisibleOnFront(int $value)
 * @method int getIsHtmlAllowedOnFront()
 * @method \Magento\Catalog\Model\Entity\Attribute setIsHtmlAllowedOnFront(int $value)
 * @method int getIsUsedForPriceRules()
 * @method \Magento\Catalog\Model\Entity\Attribute setIsUsedForPriceRules(int $value)
 * @method int getIsFilterableInSearch()
 * @method \Magento\Catalog\Model\Entity\Attribute setIsFilterableInSearch(int $value)
 * @method \Magento\Catalog\Model\Entity\Attribute setUsedInProductListing(int $value)
 * @method \Magento\Catalog\Model\Entity\Attribute setUsedForSortBy(int $value)
 * @method \Magento\Catalog\Model\Entity\Attribute setApplyTo(string $value)
 * @method int getIsVisibleInAdvancedSearch()
 * @method \Magento\Catalog\Model\Entity\Attribute setIsVisibleInAdvancedSearch(int $value)
 * @method \Magento\Catalog\Model\Entity\Attribute setPosition(int $value)
 * @method int getIsWysiwygEnabled()
 * @method \Magento\Catalog\Model\Entity\Attribute setIsWysiwygEnabled(int $value)
 * @method int getIsUsedForPromoRules()
 * @method \Magento\Catalog\Model\Entity\Attribute setIsUsedForPromoRules(int $value)
 */
class Attribute extends \Magento\Eav\Model\Entity\Attribute
    implements \Magento\Catalog\Api\Data\ProductAttributeInterface
{
    /**
     * Event Prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'catalog_entity_attribute';

    /**
     * Event Object
     *
     * @var string
     */
    protected $_eventObject = 'attribute';

    const MODULE_NAME = 'Magento_Catalog';

    /**
     * @var LockValidatorInterface
     */
    protected $attrLockValidator;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\Resource\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Catalog\Model\Product\ReservedAttributeList $reservedAttributeList
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param LockValidatorInterface $lockValidator
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Resource\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Catalog\Model\Product\ReservedAttributeList $reservedAttributeList,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        LockValidatorInterface $lockValidator,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->attrLockValidator = $lockValidator;
        parent::__construct(
            $context,
            $registry,
            $coreData,
            $eavConfig,
            $eavTypeFactory,
            $storeManager,
            $resourceHelper,
            $universalFactory,
            $localeDate,
            $reservedAttributeList,
            $localeResolver,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Processing object before save data
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Eav\Exception
     */
    protected function _beforeSave()
    {
        try {
            $this->attrLockValidator->validate($this);
        } catch (\Magento\Framework\Model\Exception $exception) {
            throw new \Magento\Eav\Exception($exception->getMessage());
        }

        $this->setData('modulePrefix', self::MODULE_NAME);
        return parent::_beforeSave();
    }

    /**
     * Processing object after save data
     *
     * @return \Magento\Framework\Model\AbstractModel
     */
    protected function _afterSave()
    {
        /**
         * Fix saving attribute in admin
         */
        $this->_eavConfig->clear();
        return parent::_afterSave();
    }

    /**
     * {@inheritdoc}
     */
    public function isWysiwygEnabled()
    {
        return $this->getData('is_wysiwyg_enabled');
    }

    /**
     * {@inheritdoc}
     */
    public function isHtmlAllowedOnFront()
    {
        return $this->getData('is_html_allowed_on_front');
    }

    /**
     * {@inheritdoc}
     */
    public function getUsedForSortBy()
    {
        return $this->getData('used_for_sort_by');
    }

    /**
     * {@inheritdoc}
     */
    public function isFilterable()
    {
        return $this->getData('is_filterable');
    }

    /**
     * {@inheritdoc}
     */
    public function isFilterableInSearch()
    {
        return $this->getData('is_filterable_in_search');
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->getData('position');
    }

    /**
     * {@inheritdoc}
     */
    public function getApplyTo()
    {
        return $this->getData('apply_to');
    }

    /**
     * {@inheritdoc}
     */
    public function isConfigurable()
    {
        return $this->getData('is_configurable');
    }

    /**
     * {@inheritdoc}
     */
    public function isSearchable()
    {
        return $this->getData('is_searchable');
    }

    /**
     * {@inheritdoc}
     */
    public function isVisibleInAdvancedSearch()
    {
        return $this->getData('is_visible_in_advanced_search');
    }

    /**
     * {@inheritdoc}
     */
    public function isComparable()
    {
        return $this->getData('is_comparable');
    }

    /**
     * {@inheritdoc}
     */
    public function isUsedForPromoRules()
    {
        return $this->getData('is_used_for_promo_rules');
    }

    /**
     * {@inheritdoc}
     */
    public function isVisibleOnFront()
    {
        return $this->getData('is_visible_on_front');
    }

    /**
     * {@inheritdoc}
     */
    public function getUsedInProductListing()
    {
        return $this->getData('used_in_product_listing');
    }

    /**
     * {@inheritdoc}
     */
    public function isVisible()
    {
        return $this->getData('is_visible');
    }
}
