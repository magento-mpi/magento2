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
 * @package    Enterprise_CatalogPermissions
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Adminhtml permission tab on category page
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 */
class Enterprise_CatalogPermissions_Block_Adminhtml_Catalog_Category_Tab_Permissions
    extends Mage_Adminhtml_Block_Catalog_Category_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('enterprise/catalogpermissions/catalog/category/tab/permissions.phtml');
    }

    /**
     * Prepare layout
     *
     * @return Enterprise_CatalogPermissions_Block_Adminhtml_Catalog_Category_Tab_Permissions
     */
    protected function _prepareLayout()
    {
        $this->setChild('row', $this->getLayout()->createBlock(
            'enterprise_catalogpermissions/adminhtml_catalog_category_tab_permissions_row'
        ));

        $this->setChild('add_button', $this->getLayout()->createBlock('adminhtml/widget_button')
            ->addData(array(
                'label' => $this->helper('enterprise_catalogpermissions')->__('New Permission'),
                'class' => 'add' . ($this->isReadonly() ? ' disabled' : ''),
                'type'  => 'button',
                'disabled' => $this->isReadonly()
            ))
        );

        return parent::_prepareLayout();
    }

    public function getConfigJson()
    {
        $config = array(
            'row' => $this->getChildHtml('row'),
            'duplicate_message' => $this->helper('enterprise_catalogpermissions')->__('Permission with same scope already exists.'),
            'permissions'  => array()
        );

        if ($this->getCategoryId()) {
            foreach ($this->getPermissionCollection() as $permission) {
                $config['permissions']['permission' . $permission->getId()] = $permission->getData();
            }
        }
        return Zend_Json::encode($config);
    }

    /**
     * Retrieve permission collection
     *
     * @return Enterprise_CatalogPermissions_Model_Mysql4_Permission_Collection
     */
    public function getPermissionCollection()
    {
        if (!$this->hasData('permission_collection')) {
            $collection = Mage::getModel('enterprise_catalogpermissions/permission')
                ->getCollection()
                ->addFieldToFilter('category_id', $this->getCategoryId())
                ->setOrder('permission_id', 'asc');
            $this->setData('permisssion_collection', $collection);
        }

        return $this->getData('permisssion_collection');
    }

    /**
     * Retrieve tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->helper('enterprise_catalogpermissions')->__('Category Permissions');
    }

    /**
     * Retrieve tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->helper('enterprise_catalogpermissions')->__('Category Permissions');
    }

    /**
     * Tab visibility
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab visibility
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Retrieve add button html
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * Check is block readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getCategory()->getPermissionsReadonly();
    }
}
