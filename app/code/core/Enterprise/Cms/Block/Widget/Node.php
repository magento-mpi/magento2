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
 * Cms Hierarchy Node Widget Block
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Block_Widget_Node extends Enterprise_Cms_Block_Widget_Abstract
{
    /**
     * Define default template and settings
     *
     */
    protected function _construct()
    {
        $this->setTemplate('enterprise/widget/nodelink.phtml');
    }

    /**
     * Retrieve specified anchor text
     *
     * @return string
     */
    public function getLinkTitle()
    {
        if ($this->getData('anchor_text')) {
            return $this->getData('anchor_text');
        }
        return $this->getNode()->getLabel();
    }

    /**
     * Retrieve link specified title
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->getData('title')) {
            return $this->getData('title');
        }
        return $this->getNode()->getLabel();
    }

    /**
     * Retrieve Node URL
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->getNode()->getUrl();
    }

    /**
     * Retrieve additional link attributes
     *
     * @return string
     */
    public function getLinkAttributes()
    {
        $allow = array(
            'charset', 'type', 'name', 'hreflang', 'rel', 'rev', 'accesskey', 'shape',
            'coords', 'tabindex', 'onfocus', 'onblur', // %attrs
            'id', 'class', 'style', // %coreattrs
            'lang', 'dir', // %i18n
            'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove',
            'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup' // %events
        );
        $attributes = array();
        foreach ($allow as $attribute) {
            if ($this->hasData($attribute)) {
                $attributes[$attribute] = $this->_getData($attribute);
            }
        }

        if (!empty($attributes)) {
            return $this->serialize($attributes);
        }
        return '';
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getNode()) {
            return '';
        }
        return parent::_toHtml();
    }
}
