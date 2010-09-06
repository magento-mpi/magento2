<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product attribute extension with event dispatching
 *
 * @method Mage_Catalog_Model_Resource_Attribute _getResource()
 * @method Mage_Catalog_Model_Resource_Attribute getResource()
 * @method Mage_Catalog_Model_Entity_Attribute getFrontendInputRenderer()
 * @method string setFrontendInputRenderer(string $value)
 * @method int setIsGlobal(int $value)
 * @method Mage_Catalog_Model_Entity_Attribute getIsVisible()
 * @method int setIsVisible(int $value)
 * @method Mage_Catalog_Model_Entity_Attribute getIsSearchable()
 * @method int setIsSearchable(int $value)
 * @method Mage_Catalog_Model_Entity_Attribute getSearchWeight()
 * @method int setSearchWeight(int $value)
 * @method Mage_Catalog_Model_Entity_Attribute getIsFilterable()
 * @method int setIsFilterable(int $value)
 * @method Mage_Catalog_Model_Entity_Attribute getIsComparable()
 * @method int setIsComparable(int $value)
 * @method int setIsVisibleOnFront(int $value)
 * @method Mage_Catalog_Model_Entity_Attribute getIsHtmlAllowedOnFront()
 * @method int setIsHtmlAllowedOnFront(int $value)
 * @method Mage_Catalog_Model_Entity_Attribute getIsUsedForPriceRules()
 * @method int setIsUsedForPriceRules(int $value)
 * @method Mage_Catalog_Model_Entity_Attribute getIsFilterableInSearch()
 * @method int setIsFilterableInSearch(int $value)
 * @method Mage_Catalog_Model_Entity_Attribute getUsedInProductListing()
 * @method int setUsedInProductListing(int $value)
 * @method Mage_Catalog_Model_Entity_Attribute getUsedForSortBy()
 * @method int setUsedForSortBy(int $value)
 * @method Mage_Catalog_Model_Entity_Attribute getIsConfigurable()
 * @method int setIsConfigurable(int $value)
 * @method Mage_Catalog_Model_Entity_Attribute getApplyTo()
 * @method string setApplyTo(string $value)
 * @method Mage_Catalog_Model_Entity_Attribute getIsVisibleInAdvancedSearch()
 * @method int setIsVisibleInAdvancedSearch(int $value)
 * @method Mage_Catalog_Model_Entity_Attribute getPosition()
 * @method int setPosition(int $value)
 * @method Mage_Catalog_Model_Entity_Attribute getIsWysiwygEnabled()
 * @method int setIsWysiwygEnabled(int $value)
 * @method Mage_Catalog_Model_Entity_Attribute getIsUsedForPromoRules()
 * @method int setIsUsedForPromoRules(int $value)
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Entity_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    protected $_eventPrefix = 'catalog_entity_attribute';
    protected $_eventObject = 'attribute';
    const MODULE_NAME = 'Mage_Catalog';

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if ($this->_getResource()->isUsedBySuperProducts($this)) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('This attribute is used in configurable products'));
        }
        $this->setData('modulePrefix', self::MODULE_NAME);
        return parent::_beforeSave();
    }

    /**
     * Processing object after save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        /**
         * Fix saving attribute in admin
         */
        Mage::getSingleton('eav/config')->clear();
        return parent::_afterSave();
    }
}
