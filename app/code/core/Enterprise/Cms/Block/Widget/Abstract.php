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
 * Cms Template Filter Abstract Block
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
abstract class Enterprise_Cms_Block_Widget_Abstract
    extends Mage_Core_Block_Template
    implements Mage_Cms_Block_Widget_Interface
{
    /**
     * Current Hierarchy Tree Instance
     *
     * @var Enterprise_Cms_Model_Hierarchy
     */
    protected $_hierarchy;

    /**
     * Current Hierarchy Node Page Instance
     *
     * @var Enterprise_Cms_Model_Hierarchy_Node
     */
    protected $_node;

    /**
     * Retrieve current hierarchy Tree instance
     *
     * @return Enterprise_Cms_Model_Hierarchy
     */
    public function getHierarchy()
    {
        if (is_null($this->_hierarchy)) {
            if (!$this->getNode()) {
                $this->_hierarchy = false;
            } else {
                $this->_hierarchy = $this->getNode()->getHierarchy();
            }
        }
        return $this->_hierarchy;
    }

    /**
     * Retrieve current hierarchy Node Page instance
     *
     * @return Enterprise_Cms_Model_Hierarchy_Node
     */
    public function getNode()
    {
        if (is_null($this->_node)) {
            if ($this->getNodeId()) {
                $this->_node = Mage::getModel('enterprise_cms/hierarchy_node')
                    ->load($this->getNodeId());
            } else {
                $this->_node = Mage::registry('current_cms_hierarchy_node');
            }
        }
        return $this->_node;
    }
}
