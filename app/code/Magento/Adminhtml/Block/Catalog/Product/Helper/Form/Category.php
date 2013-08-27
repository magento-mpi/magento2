<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product form category field helper
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Category extends Magento_Data_Form_Element_Multiselect
{
    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Backend data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_backendData = null;

    /**
     * @param Magento_Backend_Helper_Data $backendData
     * @param Magento_Core_Helper_Data $coreData
     * @param  $attributes
     * @param  $layout
     */
    public function __construct(
        Magento_Backend_Helper_Data $backendData,
        Magento_Core_Helper_Data $coreData,
        $attributes = array(),
        Magento_Core_Model_Layout $layout = null
    ) {
        $this->_backendData = $backendData;
        $this->_coreData = $coreData;
        parent::__construct($attributes);
        $this->_layout = $layout ?: Mage::getObjectManager()->get('Magento_Core_Model_Layout');
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
     * @return Magento_Catalog_Model_Resource_Category_Collection
     */
    protected function _getCategoriesCollection()
    {
        return Mage::getResourceModel('Magento_Catalog_Model_Resource_Category_Collection');
    }

    /**
     * Attach category suggest widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        /** @var $coreHelper Magento_Core_Helper_Data */
        $coreHelper = $this->_coreData;
        $htmlId = $this->getHtmlId();
        $suggestPlaceholder = __('start typing to search category');
        $selectorOptions = $coreHelper->jsonEncode($this->_getSelectorOptions());
        $newCategoryCaption = __('New Category');

        $button = $this->_layout
            ->createBlock('Magento_Backend_Block_Widget_Button')
            ->setData(array(
                'id'        => 'add_category_button',
                'label'     => $newCategoryCaption,
                'title'     => $newCategoryCaption,
                'onclick'   => 'jQuery("#new-category").dialog("open")'
            ));
        $return = <<<HTML
    <input id="{$htmlId}-suggest" placeholder="$suggestPlaceholder" />
    <script>
        jQuery('#{$htmlId}-suggest').mage('treeSuggest', {$selectorOptions});
    </script>
HTML;
        return $return . $button->toHtml();
    }

    /**
     * Get selector options
     *
     * @return array
     */
    protected function _getSelectorOptions()
    {
        return array(
            'source' => $this->_backendData
                ->getUrl('adminhtml/catalog_category/suggestCategories'),
            'valueField' => '#' . $this->getHtmlId(),
            'className' => 'category-select',
            'multiselect' => true,
            'showAll' => true
        );
    }
}
