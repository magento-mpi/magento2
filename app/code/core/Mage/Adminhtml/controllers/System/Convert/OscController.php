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
 * Convert GUI admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     MKyaw Soe Lynn<mvincent@varien.com>
 */
class Mage_Adminhtml_System_Convert_OscController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('adminhtml/system_convert_osc');
        return $this;
    }

    public function indexAction()
    {
    	$this->_initAction();
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_convert_osc')
        );
        $this->renderLayout();
    }

    public function editAction()
    {

    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {

    }

    public function deleteAction()
    {

    }
}