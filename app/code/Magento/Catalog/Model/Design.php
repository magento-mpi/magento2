<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

/**
 * Catalog Custom Category design Model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Design extends \Magento\Framework\Model\AbstractModel
{
    const APPLY_FOR_PRODUCT = 1;

    const APPLY_FOR_CATEGORY = 2;

    /**
     * Design package instance
     *
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $_design = null;

    /**
     * @var \Magento\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_localeDate = $localeDate;
        $this->_design = $design;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Apply custom design
     *
     * @param string $design
     * @return $this
     */
    public function applyCustomDesign($design)
    {
        $this->_design->setDesignTheme($design);
        return $this;
    }

    /**
     * Get custom layout settings
     *
     * @param \Magento\Catalog\Model\Category|\Magento\Catalog\Model\Product $object
     * @return \Magento\Object
     */
    public function getDesignSettings($object)
    {
        if ($object instanceof \Magento\Catalog\Model\Product) {
            $currentCategory = $object->getCategory();
        } else {
            $currentCategory = $object;
        }

        $category = null;
        if ($currentCategory) {
            $category = $currentCategory->getParentDesignCategory($currentCategory);
        }

        if ($object instanceof \Magento\Catalog\Model\Product) {
            if ($category && $category->getCustomApplyToProducts()) {
                return $this->_mergeSettings($this->_extractSettings($category), $this->_extractSettings($object));
            } else {
                return $this->_extractSettings($object);
            }
        } else {
            return $this->_extractSettings($category);
        }
    }

    /**
     * Extract custom layout settings from category or product object
     *
     * @param \Magento\Catalog\Model\Category|\Magento\Catalog\Model\Product $object
     * @return \Magento\Object
     */
    protected function _extractSettings($object)
    {
        $settings = new \Magento\Object();
        if (!$object) {
            return $settings;
        }
        $date = $object->getCustomDesignDate();
        if (array_key_exists(
            'from',
            $date
        ) && array_key_exists(
            'to',
            $date
        ) && $this->_localeDate->isScopeDateInInterval(
            null,
            $date['from'],
            $date['to']
        )
        ) {
            $settings->setCustomDesign(
                $object->getCustomDesign()
            )->setPageLayout(
                $object->getPageLayout()
            )->setLayoutUpdates(
                (array)$object->getCustomLayoutUpdate()
            );
        }
        return $settings;
    }

    /**
     * Merge custom design settings
     *
     * @param \Magento\Object $categorySettings
     * @param \Magento\Object $productSettings
     * @return \Magento\Object
     */
    protected function _mergeSettings($categorySettings, $productSettings)
    {
        if ($productSettings->getCustomDesign()) {
            $categorySettings->setCustomDesign($productSettings->getCustomDesign());
        }
        if ($productSettings->getPageLayout()) {
            $categorySettings->setPageLayout($productSettings->getPageLayout());
        }
        if ($productSettings->getLayoutUpdates()) {
            $update = array_merge($categorySettings->getLayoutUpdates(), $productSettings->getLayoutUpdates());
            $categorySettings->setLayoutUpdates($update);
        }
        return $categorySettings;
    }
}
