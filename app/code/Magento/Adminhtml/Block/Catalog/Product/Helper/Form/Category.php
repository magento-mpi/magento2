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
namespace Magento\Adminhtml\Block\Catalog\Product\Helper\Form;

class Category extends \Magento\Data\Form\Element\Multiselect
{
    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    public function __construct($attributes = array(), \Magento\Core\Model\Layout $layout = null)
    {
        parent::__construct($attributes);
        $this->_layout = $layout ?: \Mage::getObjectManager()->get('Magento\Core\Model\Layout');
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
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    protected function _getCategoriesCollection()
    {
        return \Mage::getResourceModel('Magento\Catalog\Model\Resource\Category\Collection');
    }

    /**
     * Attach category suggest widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        /** @var $coreHelper \Magento\Core\Helper\Data */
        $coreHelper = \Mage::helper('Magento\Core\Helper\Data');
        $htmlId = $this->getHtmlId();
        $suggestPlaceholder = __('start typing to search category');
        $selectorOptions = $coreHelper->jsonEncode($this->_getSelectorOptions());
        $newCategoryCaption = __('New Category');

        $button = $this->_layout
            ->createBlock('Magento\Backend\Block\Widget\Button')
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
            'source' => \Mage::helper('Magento\Backend\Helper\Data')
                ->getUrl('adminhtml/catalog_category/suggestCategories'),
            'valueField' => '#' . $this->getHtmlId(),
            'className' => 'category-select',
            'multiselect' => true,
            'showAll' => true
        );
    }
}
