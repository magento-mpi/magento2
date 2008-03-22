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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Osc edit form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Kayw Soe Lynn Maung <vincent@varien.com>
 */

class Mage_Oscommerce_Block_Adminhtml_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

//    public function __construct()
//    {
//        parent::__construct();
//        $this->setId('system_convert_osc_form');
//        $this->setTitle(Mage::helper('adminhtml')->__('OsCommerce Importing Configuration Wizard'));
//    }

//    protected function _prepareForm()
//    {
//        $model = Mage::registry('system_convert_osc');
//  
//
//        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));
//
//        if ($model->getId()) {
//            $form->addField('import_id', 'hidden', array(
//                'name' => 'import_id',
//            ));
//        }
//
//        $form->addField('name', 'text', array(
//          'label'     => $this->__('Name'),
//          'title'     => $this->__('Name'),
//          'name'      => 'name',
//          'required'  => true,
//        ));
//        
//        $form->addField('host', 'text', array(
//          'label'     => $this->__('IP or Hostname'),
//          'title'     => $this->__('IP or Hostname'),
//          'name'      => 'host',
//          'required'  => true,
//        ));
//
//        $form->addField('port', 'text', array(
//          'label'     => $this->__('Port (Default as 3360)'),
//          'title'     => $this->__('Port (Default as 3360)'),
//          'name'      => 'port',
//          'required'  => true,
//          'value'     => $model->getData('port') ? $model->getData('port'): Mage_Oscommerce_Model_Oscommerce::DEFAULT_PORT
//        ));
//                
//        $form->addField('db_name', 'text', array(
//          'label'     => $this->__('DB Name'),
//          'title'     => $this->__('DB Name'),
//          'name'      => 'db_name',
//          'required'  => true,
//        ));
//                
//        $form->addField('db_user', 'text', array(
//          'label'     => $this->__('DB Username'),
//          'title'     => $this->__('DB Username'),
//          'name'      => 'db_user',
//          'required'  => true,
//        ));
//
//        $form->addField('db_password', 'password', array(
//          'label'     => $this->__('DB Password'),
//          'title'     => $this->__('DB Password'),
//          'name'      => 'db_password',
//          'required'  => true,
//        ));
//        
//        $form->addField('run', 'button', array(
//          'label'     => $this->__('Run'),
//          'title'     => $this->__('Run'),
//          'name'      => 'run',
//          'required'  => true,
//          'width'     => '200'
//        ));
//        
//        if ($model->getId())
//          $form->setValues($model->getData());
//        $form->setUseContainer(true);
//        $form->setAction( $form->getAction() . 'ret/' . $this->getRequest()->getParam('ret') );
//        $this->setForm($form);
//
//        return parent::_prepareForm();
//    }
    
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'POST'));

        $model = Mage::registry('current_convert_osc');

        if ($model->getId()) {
            $form->addField('import_id', 'hidden', array(
                'name' => 'import_id',
            ));
            $form->setValues($model->getData());
        }
        
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }    
}
