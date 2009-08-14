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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category chooser for Wysiwyg CMS widget
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Widget_Chooser extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    /**
     * Block construction
     * Defines tree template and init tree params
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/category/widget/tree.phtml');
        $this->_withProductCount = false;
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = $element->getId() . md5(microtime());
        $sourceUrl = $this->getUrl('*/catalog_category_widget/chooser', array('uniq_id' => $uniqId));

        $chooserHtml = $this->getLayout()->createBlock('adminhtml/cms_page_edit_wysiwyg_widget_chooser')
            ->setElement($element)
            ->setSourceUrl($sourceUrl)
            ->toHtml();

        $element->setData('after_element_html', $chooserHtml);
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
        $js = '
            function (node, e) {
                var chooser = $("tree'.$this->getId().'").up().previous("a.widget-option-chooser");

                var optionLabel = node.text;
                var optionValue = node.attributes.id;

                chooser.previous("input.widget-option").value = "category/" + optionValue;
                chooser.next("label.widget-option-label").update(optionLabel);

                var responseContainerId = "responseCnt" + chooser.id;
                $(responseContainerId).hide();
            }
        ';
        return $js;
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @param Varien_Data_Tree_Node|array $node
     * @param int $level
     * @return string
     */
    protected function _getNodeJson($node, $level = 0)
    {
        $item = parent::_getNodeJson($node, $level);
        $item['url_key'] = $node->getData('url_key');
        return $item;
    }

    /**
     * Adds some extra params to categories collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
     */
    public function getCategoryCollection()
    {
        return parent::getCategoryCollection()->addAttributeToSelect('url_key');
    }

    /**
     * Tree JSON source URL
     *
     * @return string
     */
    public function getLoadTreeUrl($expanded=null)
    {
        return $this->getUrl('*/catalog_category_widget/categoriesJson', array('_current'=>true));
    }
}
