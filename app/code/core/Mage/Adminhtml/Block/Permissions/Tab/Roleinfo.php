<?php
/**
 * implementing now
 *
 */
class Mage_Adminhtml_Block_Permissions_Tab_Roleinfo extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    public function _beforeToHtml() {
    	$this->_initForm();

    	return parent::_beforeToHtml();
    }

    protected function _initForm()
    {
        $roleId = $this->getRequest()->getParam('rid');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Role Information')));

        $fieldset->addField('role_name', 'text',
            array(
                'name'  => 'role_name',
                'label' => __('Role Title'),
                'id'    => 'role_name',
                'title' => __('Role Title'),
                'class' => 'required-entry',
                'required' => true,
            )
        );

        $fieldset->addField('role_id', 'hidden',
            array(
                'name'  => 'role_id',
                'id'    => 'role_id',
            )
        );

        $roles = Mage::getResourceModel('permissions/roles_collection')
            ->load();

        $opt = array(array('role_id'=>0, 'tree_level'=>0, 'role_name'=>'Root', 'value' => '0', 'label' => 'Root'));
        $tmpArr = array();
        foreach( $roles as $role ) {
            if( $role->getRoleId() == $roleId ) {
                continue;
            }
            $tmpArr['value'] = $role->getRoleId();
            $tmpArr['role_id'] = $role->getRoleId();
            $tmpArr['parent_id'] = $role->getParentId();
            $tmpArr['tree_level'] = $role->getTreeLevel();
            $tmpArr['label'] = '|' . str_repeat('-', $role->getTreeLevel()) . $role->getRoleName();
            $rolesArray[] = $tmpArr;
        }

        foreach ($rolesArray as $r) {
            foreach ($opt as $i=>$o) {
                if ($r['parent_id']==$o['role_id']) {
                    array_splice($opt, $i+1, 0, array($r));
                    break;
                }
            }
        }

        $fieldset->addField('parent_id', 'select',
            array(
                'name'  => 'parent_id',
                'label' => __('Role Parent'),
                'id'    => 'parent_id',
                'title' => __('Role Parent'),
                'class' => 'required-entry',
                'required' => true,
                'values'=> $opt,
            )
        );

        $form->setValues($this->getRole()->getData());
        $this->setForm($form);
    }
}
