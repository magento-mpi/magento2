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
 * @category   Enterprise
 * @package    Enterprise_Pci
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Locked administrators grid
 *
 */
class Enterprise_Pci_Block_Adminhtml_Locks_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Instantiate collection
     *
     * @return unknown
     */
    public function getCollection()
    {
        if (!$this->_collection) {
            $this->_collection = Mage::getResourceModel('admin/user_collection');
        }
        return $this->_collection;
    }

    /**
     * Prepare grid columns
     *
     * @return Enterprise_Pci_Block_Adminhtml_Locks_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('user_id', array(
                'header' => Mage::helper('enterprise_pci')->__('ID'),
                'index'  => 'user_id',
                'width'  => 50,
                'filter' => false,
        ));
        $this->addColumn('username', array(
                'header' => Mage::helper('enterprise_pci')->__('Username'),
                'index'  => 'username',
        ));
        $this->addColumn('lock_expires', array(
                'header' => Mage::helper('enterprise_pci')->__('Lock expires'),
                'index'  => 'lock_expires',
                'filter' => false,
        ));
        $this->addColumn('unlock', array(
                'header' => Mage::helper('enterprise_pci')->__('Unlock'),
                'width' => 50,
                'filter' => false,
                'sortable' => false,
        ));

/*
firstname,lastname,email,username,password,created,modified,logdate,lognum,reload_acl_flag,is_active,extra,failed_login_attempts,lock_expires
*/
        return parent::_prepareColumns();
    }
}
