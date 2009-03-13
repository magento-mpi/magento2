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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Staging Manage controller
 */
class Enterprise_Staging_Staging_ManageController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Enterprise_Staging');
    }

    /**
     * Initialize staging from request parameters
     *
     * @return Enterprise_Staging_Model_Staging
     */
    protected function _initStaging()
    {
        $stagingId  = (int) $this->getRequest()->getParam('id');
        $staging    = Mage::getModel('enterprise_staging/staging');

        if (!$stagingId) {
            if ($websiteIds = $this->getRequest()->getParam('websites')) {
                $staging->setMasterWebsiteIds($websiteIds);
            }

            if ($setId = (int) $this->getRequest()->getParam('set')) {
                $staging->setDatasetId($setId);
            }

            if ($type = $this->getRequest()->getParam('type')) {
                $staging->setType($type);
            }
        }

        if ($stagingId) {
            $staging->load($stagingId);
        }

        Mage::register('staging', $staging);

        return $staging;
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Staging edit form
     */
    public function editAction()
    {
        $staging = $this->_initStaging();

		$websiteIds = (array) $staging->getMasterWebsiteIds();
		if ($websiteIds) {
			foreach ($websiteIds as $websiteId) {
				$website = Mage::app()->getWebsite($websiteId);
				if ($website->getIsStaging()) {
				    $this->_getSession()->addError('Some of selected website is staging. Please. select another one.');
				    $this->_redirect('*/*/edit', array('_current' => false));
				    return $this;
				}
		    }
		}

        Mage::dispatchEvent('staging_edit_action', array('staging' => $staging));

        $_additionalLayoutPart = '';
        if (!$staging->getId()) {
            $_additionalLayoutPart = '_new';
        }

        $this->loadLayout(array(
            'default',
            strtolower($this->getFullActionName()),
            'staging_'.$staging->getType() . $_additionalLayoutPart
        ));

        $this->_setActiveMenu('enterprise/staging');

        $this->renderLayout();
        Mage::dispatchEvent('on_staging_edit_after', array('staging' => $staging));
    }

    /**
     * Staging grid for AJAX request
     */
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('staging/grid')->toHtml()
        );
    }

    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        try {
            $stagingData = $this->getRequest()->getPost('staging');
            Mage::getModel('enterprise_staging/staging')
                ->setId($this->getRequest()->getParam('id'))
                ->addData($stagingData)
                ->validate();
        } catch (Enterprise_Staging_Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Initialize staging before saving
     */
    protected function _initStagingSave()
    {
        $staging = $this->_initStaging();

        $stagingData = $this->getRequest()->getPost('staging');

        $items = isset($stagingData['items']) ? $stagingData['items'] : false;
        if ($items) {
            foreach ($items as $itemData) {
                if (isset($itemData['dataset_item_id'])) {
                    if (!isset($itemData['staging_item_id'])) {
                        $item = Mage::getModel('enterprise_staging/staging_item');
                        $item->addData($itemData);
                        $staging->addItem($item);
                    } else {
                        $code = $itemData['code'];
                        $item = $staging->getItemsCollection()->getItemByCode($code);
                        $item->addData($itemData);
                    }
                } else {
                    if (isset($itemData['staging_item_id'])) {
                        $code = $itemData['code'];
                        $item = $staging->getItemsCollection()->getItemByCode($code);
                        $item->isDeleted(true);
                    }
                }
            }
        }

        $websites = isset($stagingData['websites']) ? $stagingData['websites'] : false;
        if ($websites) {
            $existWebsites = Mage::getResourceSingleton('enterprise_staging/staging')->getWebsiteIds($staging);
            foreach ($websites as $websiteData) {
            	$websiteId = $websiteData['master_website_id'];
                if (in_array($websiteId, $existWebsites)) {
                    $code = $websiteData['code'];
                    $item = $staging->getWebsitesCollection()->getItemByCode($code);
                    $item->addData($websiteData);
                } else {
                    $website = Mage::getModel('enterprise_staging/staging_website');
                    $website->addData($websiteData);
                    $staging->addWebsite($website);
                }
            }
        }

        /**
         * Initialize general data for staging
         */

        $staging->addData($stagingData);

        /**
         * Initialize data for configurable staging
         */
        $data = $this->getRequest()->getPost('configurable_data');
        if ($data) {
            $staging->setConfigurableData(Zend_Json::decode($data));
        }

        Mage::dispatchEvent('enterprise_staging_prepare_save', array('staging' => $staging, 'request' => $this->getRequest()));

        return $staging;
    }

    /**
     * Save staging action
     */
    public function saveAction()
    {
        $websiteIds     = $this->getRequest()->getParam('websites');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $stagingId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);

        $data = $this->getRequest()->getPost();

        if ($data) {
            $staging = $this->_initStagingSave();

            try {
                $staging->save();
                $this->_getSession()->addSuccess($this->__('Staging was successfully saved.'));
                $stagingId = $staging->getId();
                Mage::dispatchEvent('on_enterprise_staging_save', array('staging' => $staging));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setStagingData($data);
                $redirectBack = true;
            } catch (Exception $e) {
                $this->_getSession()->addException($e, $e->getMessage());
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $stagingId,
                '_current'=>true
            ));
        } elseif ($this->getRequest()->getParam('popup')) {
            $this->_redirect('*/*/created', array(
                '_current'   => true,
                'id'         => $stagingId,
                'edit'       => $isEdit
            ));
        } else {
            $this->_redirect('*/*/', array('website' => $websiteId));
        }
    }

    public function deleteAction()
    {
    	$id = $this->getRequest()->getParam('id');
        if ($id) {
            $staging = Mage::getModel('enterprise_staging/staging')->setId($id);

            try {
                Mage::dispatchEvent('enterprise_staging_controller_staging_delete', array('staging'=>$staging));
                $staging->delete();
                $this->_getSession()->addSuccess($this->__('Staging deleted'));
            } catch (Exception $e){
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('website'=>$this->getRequest()->getParam('website'))));
    }

    public function syncAction()
    {

    }

    public function mergeAction()
    {
    	$this->_initStaging();

    	$this->loadLayout();

        $this->_setActiveMenu('enterprise/staging');

        $this->renderLayout();
    }

    public function mergePostAction()
    {
        $redirectBack   = $this->getRequest()->getParam('back', false);

        $staging = $this->_initStaging();

        $mapData = $this->getRequest()->getPost('map');

        if ($mapData) {
            try {
                $staging->save();

                $staging->getMapperInstance()->setMapData($mapData);
                $staging->merge();
                $staging->setEventCode('merge');
                $staging->setState(Enterprise_Staging_Model_Staging_Config::STATE_MERGED);
                $staging->setStatus(Enterprise_Staging_Model_Staging_Config::STATUS_MERGED);
                $staging->save();

                $this->_getSession()->addSuccess($this->__('Staging was successfully merged.'));
                $stagingId = $staging->getId();
                Mage::dispatchEvent('on_enterprise_staging_merge', array('staging' => $staging));
            } catch (Mage_Core_Exception $e) {
                echo $e;
                STOP();
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            } catch (Exception $e) {
                echo $e;
                STOP();
                $this->_getSession()->addException($e, $e->getMessage());
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'        => $stagingId,
                '_current'  =>true
            ));
        } else {
            $this->_redirect('*/*/');
        }
    }

    public function rollbackAction()
    {
        $redirectBack   = $this->getRequest()->getParam('back', false);

        $staging = $this->_initStaging();

        try {
            $staging->rollback();
            $this->_getSession()->addSuccess($this->__('Staging master website was successfully restored.'));
            $stagingId = $staging->getId();
            Mage::dispatchEvent('on_enterprise_staging_rollback', array('staging' => $staging));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $redirectBack = true;
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $e->getMessage());
            $redirectBack = true;
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'        => $stagingId,
                '_current'  =>true
            ));
        } else {
            $this->_redirect('*/*/');
        }
    }

//    protected function _isAllowed()
//    {
//        return Mage::getSingleton('admin/session')->isAllowed('enterprise/staging');
//    }
}