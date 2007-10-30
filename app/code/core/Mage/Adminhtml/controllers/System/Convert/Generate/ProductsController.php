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
 * Convert admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Adminhtml_System_Convert_Generate_ProductsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('system/convert');

        /**
         * Append profiles block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_convert_generate_products', 'convert_generate_products')
        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(__('Import/Export Profiles'), __('Import/Export Profiles'));
        $this->_addBreadcrumb(__('Generate File/Products Profile'), __('Generate File/Products Profile'));

        $this->renderLayout();
    }

    public function generateAction()
    {
        if (!($p = $this->getRequest()->getPost('products'))) {
            $this->_redirect('*/*');
        }

        $xml = Mage::getModel('adminhtml/system_convert_generate_products')
            ->generateXml($p);

        $profile = Mage::getModel('core/convert_profile')
            ->setName($p['profile']['name'])
            ->setActionsXml($xml)
            ->save();

        $this->_redirect('*/system_convert_profile/edit', array('id'=>$profile->getId()));
    }
}