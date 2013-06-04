<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product form category field helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Category extends Varien_Data_Form_Element_Multiselect
{
    /**
     * Used for category count limitation
     *
     * @var Mage_Catalog_Model_Category_Limitation
     */
    protected $_limitation;

    public function __construct($attributes = array(), Mage_Catalog_Model_Category_Limitation $limitation = null)
    {
        parent::__construct($attributes);
        $this->_limitation = $limitation ?: Mage::getObjectManager()->get('Mage_Catalog_Model_Category_Limitation');

    }

    /**
     * Get values for select
     * @return array
     */
    public function getValues()
    {
        $collection = $this->_getCategoriesCollection();
        $values = $this->getValue();
        if (!is_array($values)) {
            $values = explode(',', $values);
        }
        $collection->addAttributeToSelect('name');
        $collection->addIdFilter($values);

        $options = array();

        foreach ($collection as $category) {
            $options[] = array(
                'label' => $category->getName(),
                'value' => $category->getId()
            );
        }
        return $options;
    }

    /**
     * Get categories collection
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    protected function _getCategoriesCollection()
    {
        return Mage::getResourceModel('Mage_Catalog_Model_Resource_Category_Collection');
    }

    /**
     * Attach category suggest widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = Mage::helper('Mage_Core_Helper_Data');
        $htmlId = $this->getHtmlId();
        $suggestPlaceholder = Mage::helper('Mage_Catalog_Helper_Data')->__('start typing to search category');
        $selectorOptions = $coreHelper->jsonEncode($this->_getSelectorOptions());
        $newCategoryCaption = Mage::helper('Mage_Catalog_Helper_Data')->__('New Category');

        $status = $this->_limitation->isCreateRestricted() ? 'disabled="disabled"' : '';
        return <<<HTML
    <input id="{$htmlId}-suggest" placeholder="$suggestPlaceholder" />
    <script>
        jQuery('#{$htmlId}-suggest').mage('treeSuggest', {$selectorOptions});
    </script>
    <button title="{$newCategoryCaption}" type="button" {$status} onclick="jQuery('#new-category').dialog('open')">
        <span><span><span>{$newCategoryCaption}</span></span></span>
    </button>
HTML;
    }

    /**
     * Get selector options
     *
     * @return array
     */
    protected function _getSelectorOptions()
    {
        return array(
            'source' => Mage::helper('Mage_Backend_Helper_Data')
                ->getUrl('adminhtml/catalog_category/suggestCategories'),
            'valueField' => '#' . $this->getHtmlId(),
            'className' => 'category-select',
            'multiselect' => true,
            'showAll' => true
        );
    }
}
