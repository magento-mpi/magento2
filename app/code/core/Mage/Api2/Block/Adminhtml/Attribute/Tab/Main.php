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
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block for rendering attributes tree list tab
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 * @method Mage_Api2_Model_Acl_Global_Role getRole()
 * @method Mage_Api2_Block_Adminhtml_Roles_Tab_Resources setRole(Mage_Api2_Model_Acl_Global_Role $role)
 */
class Mage_Api2_Block_Adminhtml_Attribute_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
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

        $this->setId('api2_attribute_section_main')
                ->setData('default_dir', Varien_Db_Select::SQL_ASC)
                ->setData('default_sort', 'sort_order')
                ->setData('title', $this->__('Attribute main'))
                ->setData('use_ajax', true);
    }

    /**
     * Prepare form object
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('adminhtml')->__('Role Information')
        ));

        $isGuestRole = $this->getRole() && $this->getRole()->isGuestRole();

        $data = array(
            'name'  => 'role_name',
            'label' => Mage::helper('adminhtml')->__('Role Name'),
            'id'    => 'role_name',
            'class' => 'required-entry',
            'required' => true,
        );
        if ($isGuestRole) {
            $data['readonly'] = 'readonly';
            $data['note'] = 'Guest role is protected.';
        }
        $fieldset->addField('role_name', 'text', $data);

        $fieldset->addField('entity_id', 'hidden',
            array(
                'name'  => 'id',
            )
        );

        $fieldset->addField('in_role_users', 'hidden',
            array(
                'name'  => 'in_role_users',
                'id'    => 'in_role_userz',
            )
        );

        $fieldset->addField('in_role_users_old', 'hidden', array('name' => 'in_role_users_old'));

        if ($this->getRole()) {
            $form->setValues($this->getRole()->getData());
        }
        $this->setForm($form);
    }


    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Attr form');
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
