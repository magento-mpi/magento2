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
 * Category chooser for Wysiwyg CMS widget
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Category_Widget_Chooser extends Magento_Adminhtml_Block_Catalog_Category_Tree
{
    protected $_selectedCategories = array();

    /**
     * Block construction
     * Defines tree template and init tree params
     */

    protected $_template = 'catalog/category/widget/tree.phtml';

    protected function _construct()
    {
        parent::_construct();

        $this->_withProductCount = false;
    }

    /**
     * Setter
     *
     * @param array $selectedCategories
     * @return Magento_Adminhtml_Block_Catalog_Category_Widget_Chooser
     */
    public function setSelectedCategories($selectedCategories)
    {
        $this->_selectedCategories = $selectedCategories;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSelectedCategories()
    {
        return $this->_selectedCategories;
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Magento_Data_Form_Element_Abstract $element Form Element
     * @return Magento_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('Magento_Core_Helper_Data')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('*/catalog_category_widget/chooser', array('uniq_id' => $uniqId, 'use_massaction' => false));

        $chooser = $this->getLayout()->createBlock('Magento_Widget_Block_Adminhtml_Widget_Chooser')
            ->setElement($element)
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        if ($element->getValue()) {
            $value = explode('/', $element->getValue());
            $categoryId = false;
            if (isset($value[0]) && isset($value[1]) && $value[0] == 'category') {
                $categoryId = $value[1];
            }
            if ($categoryId) {
                $label = Mage::getSingleton('Magento_Catalog_Model_Category')->load($categoryId)->getName();
                $chooser->setLabel($label);
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Category Tree node onClick listener js function
     *
     * @return string
     */
    public function getNodeClickListener()
    {
        if ($this->getData('node_click_listener')) {
            return $this->getData('node_click_listener');
        }
        if ($this->getUseMassaction()) {
            $js = '
                function (node, e) {
                    if (node.ui.toggleCheck) {
                        node.ui.toggleCheck(true);
                    }
                }
            ';
        } else {
            $chooserJsObject = $this->getId();
            $js = '
                function (node, e) {
                    '.$chooserJsObject.'.setElementValue("category/" + node.attributes.id);
                    '.$chooserJsObject.'.setElementLabel(node.text);
                    '.$chooserJsObject.'.close();
                }
            ';
        }
        return $js;
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @param Magento_Data_Tree_Node|array $node
     * @param int $level
     * @return string
     */
    protected function _getNodeJson($node, $level = 0)
    {
        $item = parent::_getNodeJson($node, $level);
        if (in_array($node->getId(), $this->getSelectedCategories())) {
            $item['checked'] = true;
        }
        $item['is_anchor'] = (int)$node->getIsAnchor();
        $item['url_key'] = $node->getData('url_key');
        return $item;
    }

    /**
     * Adds some extra params to categories collection
     *
     * @return Magento_Catalog_Model_Resource_Category_Collection
     */
    public function getCategoryCollection()
    {
        return parent::getCategoryCollection()->addAttributeToSelect('url_key')->addAttributeToSelect('is_anchor');
    }

    /**
     * Tree JSON source URL
     *
     * @return string
     */
    public function getLoadTreeUrl($expanded=null)
    {
        return $this->getUrl('*/catalog_category_widget/categoriesJson', array(
            '_current'=>true,
            'uniq_id' => $this->getId(),
            'use_massaction' => $this->getUseMassaction()
        ));
    }
}
