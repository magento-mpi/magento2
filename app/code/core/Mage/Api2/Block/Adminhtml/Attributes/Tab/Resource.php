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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block for rendering attributes tree list tab
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Block_Adminhtml_Acl_Attribute_Tab_Resource extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Tree model
     *
     * @var Mage_Api2_Model_Acl_Global_Rule_Tree
     */
    protected $_treeModel = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('api2_attribute_section_resources')
                ->setDefaultDir(Varien_Db_Select::SQL_ASC)
                ->setDefaultSort('sort_order')
                ->setData('title', Mage::helper('api2')->__('Attribute Rules Information'))
                ->setData('use_ajax', true);

        $this->_treeModel = Mage::getModel('api2/acl_global_rule_tree');
    }

    /**
     * Get Json Representation of Resource Tree
     *
     * @return string
     */
    public function getResTreeJson()
    {
        $data = $this->_treeModel->getTreeResources();

        /** @var $helper Mage_Core_Helper_Data */
        $helper = Mage::helper('core');
        return $helper->jsonEncode($data);
    }


    /**
     * Check if everything is allowed
     *
     * @return boolean
     */
    public function getEverythingAllowed()
    {
        return $this->_treeModel->getEverythingAllowed();
    }



    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('api2')->__('ACL Attribute Rules');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Whether tab is available
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Whether tab is visible
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
