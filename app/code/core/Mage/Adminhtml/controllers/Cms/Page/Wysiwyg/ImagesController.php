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
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Images manage controller for Cms page WYSIWYG editor
 *
 * @category   Mage
 * @package    Mage_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Adminhtml_Cms_Page_Wysiwyg_ImagesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    protected function initAction()
    {
        Mage::register('storage', $this->getStorage());
        return $this;
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function indexAction()
    {
        $this->initAction();
        $this->loadLayout('popup');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function treeJsonAction()
    {
        $this->initAction();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('enterprise_cms/adminhtml_cms_page_edit_wysiwyg_images_tree')
                ->getTreeJson()
        );
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function contentsAction()
    {
        $this->getStorage()
            ->getSession()
            ->setCurrentPath(Mage::helper('enterprise_cms/page_wysiwyg_images')->getCurrentPath());

        $this->initAction();
        $this->loadLayout('empty');
        $this->renderLayout();
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function newFolderAction()
    {
        $name = $this->getRequest()->getPost('name');
        $path = $this->getStorage()->getSession()->getCurrentPath();
        logme($name.' - '.$path);
        $this->getStorage()->createDirectory($name, $path);
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function uploadAction()
    {
        $result = array();
        try {
            $uploader = new Varien_File_Uploader('image');
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $result = $uploader->save(
                $this->getStorage()->getSession()->getCurrentPath()
            );

            $result['url'] = Mage::getSingleton('catalog/product_media_config')->getTmpMediaUrl($result['file']);
            $result['file'] = $result['file'] . '.tmp';
            $result['cookie'] = array(
                'name'     => session_name(),
                'value'    => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path'     => $this->_getSession()->getCookiePath(),
                'domain'   => $this->_getSession()->getCookieDomain()
            );
        } catch (Exception $e) {
            $result = array('error'=>$e->getMessage(), 'errorcode'=>$e->getCode());
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

    }

    /**
     * Storage model retriever
     *
     * @return Enterprise_Cms_Model_Adminhtml_Page_Wysiwyg_Images_Storage
     */
    public function getStorage()
    {
        return Mage::getModel('enterprise_cms/adminhtml_page_wysiwyg_images_storage');
    }

}
