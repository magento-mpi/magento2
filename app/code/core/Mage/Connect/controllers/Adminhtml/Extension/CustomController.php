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
 * @package     Mage_Connect
 * @subpackage  Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Extension controller
 *
 * @category    Mage
 * @package     Mage_Connect
 * @subpackage  Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */


class Mage_Connect_Adminhtml_Extension_CustomController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        Mage::app()->getStore()->setStoreId(1);
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/extension/custom');
        $this->_addContent($this->getLayout()->createBlock('connect/adminhtml_extension_custom_edit'));
        $this->_addLeft($this->getLayout()->createBlock('connect/adminhtml_extension_custom_edit_tabs'));
        $this->renderLayout();
    }

    public function resetAction()
    {
        Mage::getSingleton('connect/session')->unsCustomExtensionPackageFormData();
        $this->_redirect('*/*/edit');
    }

    public function loadAction()
    {
        $package = $this->getRequest()->getParam('id');
        if ($package) {
            $session = Mage::getSingleton('connect/session');
            try {
                $data = $this->_loadPackageFile(Mage::getBaseDir('var') . DS . 'connect' . DS . $package);
                $data = array_merge($data, array('file_name' => $package));
                $session->setCustomExtensionPackageFormData($data);
                $session->addSuccess(Mage::helper('connect')->__("Package %s data was successfully loaded", $package));
            } catch (Exception $e) {
                $session->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/edit');
    }

    /**
     *
     * TODO
     */
    private function _loadPackageFile($filenameNoExtension)
    {
        $data = null;

        // try to load xml-file
        $filename = $filenameNoExtension . '.xml';
        if (file_exists($filename)) {
            $xml = simplexml_load_file($filename);
            $data = Mage::helper('core')->xmlToAssoc($xml);
            if (!empty($data)) {
                return $data;
            }
        }

        // try to load ser-file
        $filename = $filenameNoExtension . '.ser';
        if (!is_readable($filename)) {
            throw new Exception(Mage::helper('connect')->__('Failed to load %1$s.xml or %1$s.ser', basename($filenameNoExtension)));
        }
        $contents = file_get_contents($filename);
        $data = unserialize($contents);
        if (!empty($data)) {
            return $data;
        }

        throw new Exception('Failed to load package data.');
    }

    public function saveAction()
    {
        $session = Mage::getSingleton('connect/session');
        $p = $this->getRequest()->getPost();

        if (!empty($p['_create'])) {
            $create = true;
            unset($p['_create']);
        }

        if ($p['file_name'] == '') {
            $p['file_name'] = $p['name'];
        }

        $session->setCustomExtensionPackageFormData($p);
        try {
            $ext = Mage::getModel('connect/extension');
            $ext->setData($p);
            if ($ext->savePackage()) {
                $session->addSuccess('Package data was successfully saved');
            } else {
                $session->addError('There was a problem saving package data');
                $this->_redirect('*/*/edit');
            }
            if (empty($create)) {
                $this->_redirect('*/*/edit');
            } else {
                Mage::app()->getStore()->setStoreId(1);
                $this->_forward('create');
            }
        } catch(Mage_Core_Exception $e){
            $session->addError($e->getMessage());
            $this->_redirect('*/*');
        } catch(Exception $e){
            $session->addError($e->getMessage());
            $this->_redirect('*/*');
        }
    }

    public function createAction()
    {
        $session = Mage::getSingleton('connect/session');
        try {
            $p = $this->getRequest()->getPost();
            $session->setCustomExtensionPackageFormData($p);
            $ext = Mage::getModel('connect/extension');
            $ext->setData($p);
            $ext->createPackage();
            $this->_redirect('*/*');
        } catch(Mage_Core_Exception $e){
            $session->addError($e->getMessage());
            $this->_redirect('*/*');
        } catch(Exception $e){
            $session->addError($e->getMessage());
            $this->_redirect('*/*');
        }
    }

    /**
     * Grid for loading packages
     */
    public function loadtabAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('connect/adminhtml_extension_custom_edit_tab_load')->toHtml()
        );
    }

    /**
     * Grid for loading packages
     */
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('connect/adminhtml_extension_custom_edit_tab_grid')->toHtml()
        );
    }

}
