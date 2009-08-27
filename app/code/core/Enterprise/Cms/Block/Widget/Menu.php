<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Cms Widget Menu Block
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Block_Widget_Menu extends Enterprise_Cms_Block_Widget_Abstract
{
    const TAG_UL    = 'ul';
    const TAG_OL    = 'ol';
    const TAG_LI    = 'li';

    /**
     * Allowed attributes for UL/OL/LI tags
     *
     * @var array
     */
    protected $_allowedListAttributes = array();

    /**
     * Allowed attributes for A tag
     *
     * @var array
     */
    protected $_allowedLinkAttributes = array();

    /**
     * Allowed attributes for SPAN tag (selected item)
     *
     * @var array
     */
    protected $_allowedSpanAttributes = array();

    /**
     * Initialize allowed Tags attributes
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_allowedListAttributes = array('start', 'value', 'compact', // %attrs
            'id', 'class', 'style', 'title', // %coreattrs
            'lang', 'dir', // %i18n
            'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove',
            'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup' // %events
        );
        $this->_allowedLinkAttributes = array(
            'charset', 'type', 'name', 'hreflang', 'rel', 'rev', 'accesskey', 'shape',
            'coords', 'tabindex', 'onfocus', 'onblur', // %attrs
            'id', 'class', 'style', 'title', // %coreattrs
            'lang', 'dir', // %i18n
            'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove',
            'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup' // %events
        );
        $this->_allowedSpanAttributes = array('id', 'class', 'style', 'title', // %coreattrs
            'lang', 'dir', // %i18n
            'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove',
            'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup' // %events
        );
    }

    /**
     * Retrieve list container TAG
     *
     * @return string
     */
    public function getListContainer()
    {
        $ordered = 1;
        if ($this->hasData('ordered') && $this->getOrdered() !== '') {
            $ordered = $this->getOrdered();
        }
        return (int)$ordered ? self::TAG_OL : self::TAG_UL;
    }

    /**
     * Retrieve List container type attribute
     *
     * @return string
     */
    public function getListType()
    {
        if ($this->hasData('list_type')) {
            $type = $this->_getData('list_type');
            if ($this->getListContainer() == self::TAG_OL) {
                if (in_array($type, array('A','a','I','i'))) {
                    return $type;
                }
            } else if ($this->getListContainer() == self::TAG_UL) {
                if (in_array($type, array('disc', 'circle', 'square'))) {
                    return $type;
                }
            }
        }
        return false;
    }

    /**
     * Retrieve Node Replace pairs
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $node
     * @return array
     */
    protected function _getNodeReplacePairs($node)
    {
        return array(
            '__ID__'    => $node->getId(),
            '__LABEL__' => $node->getLabel(),
            '__HREF__'  => $node->getUrl()
        );
    }

    /**
     * Retrieve list begin tag
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $node
     * @return string
     */
    protected function _getListTagBegin()
    {
        $template = $this->_getData('_list_template');
        if (!$template) {
            $template = '<' . $this->getListContainer();
            $type = $this->getListType();
            if ($type) {
                $template .= ' type="'.$type.'"';
            }
            foreach ($this->_allowedListAttributes as $attribute) {
                $value = $this->getData('list_' . $attribute);
                if (!empty($value)) {
                    $template .= ' '.$attribute.'="'.$this->htmlEscape($value).'"';
                }
            }
            if ($this->getData('list_props')) {
                $template .= ' ' . $this->getData('list_props');
            }
            $template .= '>';

            $this->setData('_list_template', $template);
        }

        return $template;
    }

    /**
     * Retrieve List end tag
     *
     * @return string
     */
    protected function _getListTagEnd()
    {
        return '</' . $this->getListContainer() . '>';
    }

    /**
     * Retrieve List Item begin tag
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $node
     * @return string
     */
    protected function _getItemTagBegin($node)
    {
        $template = $this->_getData('_item_template');
        if (!$template) {
            $template = '<' . self::TAG_LI;
            foreach ($this->_allowedListAttributes as $attribute) {
                $value = $this->getData('item_' . $attribute);
                if (!empty($value)) {
                    $template .= ' '.$attribute.'="'.$this->htmlEscape($value).'"';
                }
            }
            if ($this->getData('item_props')) {
                $template .= ' ' . $this->getData('item_props');
            }
            $template .= '>';

            $this->setData('_item_template', $template);
        }

        return strtr($template, $this->_getNodeReplacePairs($node));
    }

    /**
     * Retrieve List Item end tag
     *
     * @return string
     */
    protected function _getItemTagEnd()
    {
        return '</' . self::TAG_LI . '>';
    }

    /**
     * Retrieve Node label with link
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $node
     * @return string
     */
    protected function _getNodeLabel($node)
    {
        if ($this->_node && $this->_node->getId() == $node->getId()) {
            return $this->_getSpan($node);
        }
        return $this->_getLink($node);
    }

    /**
     * Retrieve Node label with link
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $node
     * @return string
     */
    protected function _getLink($node)
    {
        $template = $this->_getData('_link_template');
        if (!$template) {
            $template = '<a href="__HREF__"';
            foreach ($this->_allowedLinkAttributes as $attribute) {
                $value = $this->getData('link_' . $attribute);
                if (!empty($value)) {
                    $template .= ' '.$attribute.'="'.$this->htmlEscape($value).'"';
                }
            }
            $template .= '>__LABEL__</a>';
            $this->setData('_link_template', $template);
        }

        return strtr($template, $this->_getNodeReplacePairs($node));
    }

    /**
     * Retrieve Node label for current node
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $node
     * @return string
     */
    protected function _getSpan($node)
    {
        $template = $this->_getData('_span_template');
        if (!$template) {
            $template = '<span';
            foreach ($this->_allowedSpanAttributes as $attribute) {
                $value = $this->getData('span_' . $attribute);
                if (!empty($value)) {
                    $template .= ' '.$attribute.'="'.$this->htmlEscape($value).'"';
                }
            }
            $template .= '>__LABEL__</span>';
            $this->setData('_span_template', $template);
        }

        return strtr($template, $this->_getNodeReplacePairs($node));
    }

    /**
     * Retrieve tree slice array
     *
     * @return array
     */
    public function getTree()
    {
        if (!$this->hasData('_tree')) {
            $up   = $this->_getData('up');
            if (!abs(intval($up))) {
                $up = 0;
            }
            $down = $this->_getData('down');
            if (!abs(intval($down))) {
                $down = 1;
            }

            $this->setData('_tree', $this->_node->getTreeSlice($up, $down));
        }
        return $this->_getData('_tree');
    }

    /**
     * Recursive draw menu
     *
     * @param array $tree
     * @param int $parentNodeId
     * @return string
     */
    protected function _drawMenu(array $tree, $parentNodeId = 0)
    {
        if (!isset($tree[$parentNodeId])) {
            return '';
        }

        $html = $this->_getListTagBegin();
        foreach ($tree[$parentNodeId] as $nodeId => $node) {
            /* @var $node Enterprise_Cms_Model_Hierarchy_Node */

            $html .= $this->_getItemTagBegin($node) . $this->_getNodeLabel($node);
            $html .= $this->_drawMenu($tree, $nodeId);
            $html .= $this->_getItemTagEnd();
        }
        $html .= $this->_getListTagEnd();

        return $html;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_node) {
            return '';
        }
        $tree = $this->getTree();
        return $this->_drawMenu($tree);;
    }
}
