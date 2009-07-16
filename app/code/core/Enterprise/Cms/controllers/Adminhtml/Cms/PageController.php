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
 * Cms manage pages controller
 *
 * @category   Mage
 * @package    Mage_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

include('app/code/core/Mage/Adminhtml/controllers/Cms/PageController.php');

class Enterprise_Cms_Adminhtml_Cms_PageController extends Mage_Adminhtml_Cms_PageController
{

    /**
     * Prepare ans place cms page model into registry
     * with loaded data if id parameter present
     *
     * @param string $idFieldName
     * @return Mage_Cms_Model_Page
     */
    protected function _initPage($idFieldName = 'id')
    {
        $pageId = (int) $this->getRequest()->getParam($idFieldName);
        $page = Mage::getModel('cms/page');

        if ($pageId) {
            $page->load($pageId);
        }

        Mage::register('cms_page', $page);
        return $page;
    }

    public function revisionsAction()
    {
        $this->_initPage();

        $this->loadLayout();
        $this->renderLayout();
    }

    public function versionsAction()
    {
        $this->_initPage();

        $this->loadLayout();
        $this->renderLayout();
    }

    public function publishRevisionAction()
    {

    }

    public function deleteRevisionAction()
    {

    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'new':
                return Mage::getSingleton('admin/session')->isAllowed('cms/page/new');
            case 'publishRevision':
                return Mage::getSingleton('admin/session')->isAllowed('cms/page/publish_revision');
            case 'deleteRevision':
                return Mage::getSingleton('admin/session')->isAllowed('cms/page/delete_revision');
            default:
                return parent::_isAllowed();
                break;
        }
    }

}
