<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

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
 * @method int getUsedInProductListing()
 * @method \Magento\Catalog\Model\Entity\Attribute setUsedInProductListing(int $value)
 * @method int getUsedForSortBy()
 * @method \Magento\Catalog\Model\Entity\Attribute setUsedForSortBy(int $value)
 * @method int getIsConfigurable()
 * @method \Magento\Catalog\Model\Entity\Attribute setIsConfigurable(int $value)
 * @method string getApplyTo()
 * @method \Magento\Catalog\Model\Entity\Attribute setApplyTo(string $value)
 * @method int getIsVisibleInAdvancedSearch()
 * @method \Magento\Catalog\Model\Entity\Attribute setIsVisibleInAdvancedSearch(int $value)
 * @method int getPosition()
 * @method \Magento\Catalog\Model\Entity\Attribute setPosition(int $value)
 * @method int getIsWysiwygEnabled()
 * @method \Magento\Catalog\Model\Entity\Attribute setIsWysiwygEnabled(int $value)
 * @method int getIsUsedForPromoRules()
 * @method \Magento\Catalog\Model\Entity\Attribute setIsUsedForPromoRules(int $value)
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Entity;

class Attribute extends \Magento\Eav\Model\Entity\Attribute
{
    protected $_eventPrefix = 'catalog_entity_attribute';
    protected $_eventObject = 'attribute';
    const MODULE_NAME = 'Magento_Catalog';

    /**
     * Processing object before save data
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _beforeSave()
    {
        if ($this->_getResource()->isUsedBySuperProducts($this)) {
            throw \Mage::exception('Magento_Eav', __('This attribute is used in configurable products'));
        }
        $this->setData('modulePrefix', self::MODULE_NAME);
        return parent::_beforeSave();
    }

    /**
     * Processing object after save data
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _afterSave()
    {
        /**
         * Fix saving attribute in admin
         */
        \Mage::getSingleton('Magento\Eav\Model\Config')->clear();
        return parent::_afterSave();
    }
}
