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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Images manage controller for Cms page WYSIWYG editor
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Cms_Page_Wysiwyg_ImagesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init storage
     *
     * @return Mage_Adminhtml_Cms_Page_Wysiwyg_ImagesController
     */
    protected function _initAction()
    {
        $this->getStorage();
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->loadLayout('popup');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function treeJsonAction()
    {
        $this->_initAction()->_saveSessionCurrentPath();

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/cms_page_edit_wysiwyg_images_tree')
                ->getTreeJson()
        );
    }

    public function contentsAction()
    {
        $this->_initAction()->_saveSessionCurrentPath();
        $this->loadLayout('empty');
        $this->renderLayout();
    }

    public function newFolderAction()
    {
        $this->_initAction();
        $name = $this->getRequest()->getPost('name');
        $path = $this->getStorage()->getSession()->getCurrentPath();
        try {
            $result = $this->getStorage()->createDirectory($name, $path);
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function deleteFolderAction()
    {
        $path = $this->getStorage()->getSession()->getCurrentPath();
        try {
            $this->getStorage()->deleteDirectory($path);
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Files upload processing
     */
    public function uploadAction()
    {
        $this->_initAction();
        $result = array();
        try {
            $uploader = new Varien_File_Uploader('image');
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $result = $uploader->save(
                $this->getStorage()->getSession()->getCurrentPath()
            );

            $result['url'] = 'http://kd.varien.com/dev/dmitriy.volik/media/tmp/catalog/product/a/s/astrablanco_gif_3.jpg';
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
     * Image directives callback
     */
    public function imageAction()
    {
        $directive = $this->getRequest()->getParam('directive');
        $directive = Mage::helper('core')->urlDecode($directive);

        Mage::getSingleton('core/design_package')->setArea('frontend')
            ->setPackageName('enterprise')
            ->setTheme('default');

        $url = Mage::getModel('core/email_template_filter')->filter($directive);
        try {
            $image = Varien_Image_Adapter::factory('GD2');
            $image->open($url);
            $image->display();
        } catch (Exception $e) {
            $image = imagecreate(100, 100);
            $bkgrColor = imagecolorallocate($image,10,10,10);
            imagefill($image,0,0,$bkgrColor);
            $textColor = imagecolorallocate($image,255,255,255);
            imagestring($image, 4, 10, 10, 'Skin image', $textColor);
            header('Content-type: image/png');
            imagepng($image);
            imagedestroy($image);
        }
    }

    /**
     * Fire when select image
     */
    public function onInsertAction()
    {
        $url = $this->getRequest()->getParam('url');

        $this->getResponse()->setBody(

            $this->getUrl('*/cms_page_wysiwyg_images/image', array('directive' => Mage::helper('core')->urlEncode('{{media url="editor/file.jpg"}}')))
        );
    }

    /**
     * Register storage model and return it
     *
     * @return Mage_Cms_Model_Adminhtml_Page_Wysiwyg_Images_Storage
     */
    public function getStorage()
    {
        if (!Mage::registry('storage')) {
            $storage = Mage::getModel('cms/adminhtml_page_wysiwyg_images_storage');
            Mage::register('storage', $storage);
        }
        return Mage::registry('storage');
    }

    /**
     * Save current path in session
     *
     * @return Mage_Adminhtml_Cms_Page_Wysiwyg_ImagesController
     */
    protected function _saveSessionCurrentPath()
    {
        $this->getStorage()
            ->getSession()
            ->setCurrentPath(Mage::helper('cms/page_wysiwyg_images')->getCurrentPath());
        return $this;
    }
}
