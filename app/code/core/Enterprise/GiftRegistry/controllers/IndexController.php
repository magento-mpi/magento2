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
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Invitation frontend controller
 *
 * @category   Enterprise
 * @package    Enterprise_GiftRegistry
 */
class Enterprise_GiftRegistry_IndexController extends Enterprise_Enterprise_Controller_Core_Front_Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     */
    public function preDispatch()
    {
        Mage::log('GiftRegistry:indexConts Pre Disp ');
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->getResponse()->setRedirect(Mage::helper('customer')->getLoginUrl());
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }
    /**
     * View giftregistry list in 'My Account' section
     *
     */
    public function indexAction()
    {
        Mage::log('GiftRegistry: indexConts index Action');

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->loadLayoutUpdates();
        if ($block = $this->getLayout()->getBlock('giftregistry_list')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

    public function viewAction()
    {
        Mage::log('GiftRegistry: indexConts view Action');

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->loadLayoutUpdates();
        if ($block = $this->getLayout()->getBlock('giftregistry_view')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

    public function addSelectAction()
    {
        Mage::log('GiftRegistry: indexConts add Select Action');

        $this->loadLayout();
//        $this->_initLayoutMessages('customer/session');
//        $this->loadLayoutUpdates();
        if ($block = $this->getLayout()->getBlock('giftregistry_addselect')) {
//            Mage::log('2 type id = '. $typeId);
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

    /**
     * Select Type action
     */
    public function editAction()
    {
        $typeId = $this->getRequest()->getParam('type_id');
        if (!$typeId) {
            $data = $this->_getSession()->getGiftRegistryEntityFormData(true);
///            if ()
            $this->_redirect('*/*/addselect');
            return ;
        }
        $this->_initLayoutMessages('customer/session');

        /* @var $model Enterprise_GiftRegistry_Model_Entity */
        $model = Mage::getSingleton('enterprise_giftregistry/entity');
        $model->setTypeId($typeId);
        $data = $this->_getSession()->getGiftRegistryEntityFormData(true);
        if ($data) {
            $model->addData($data);
        }

        Mage::register('enterprise_giftregistry_entity', $model);

        $address = Mage::getModel('customer/address');
        $model->exportAddress($address);

        Mage::register('enterprise_giftregistry_address', $address);

        $this->loadLayout();

        $this->renderLayout();
    }

    /**
     * Create Registry action
     */
    public function editPostAction()
    {
        Mage::log('GiftRegistry: indexConts addPostAction');
        if (!($typeId = $this->getRequest()->getParam('type_id'))) {
            Mage::log('contr: AddpostAction validate false ');
//            $this->addAction($typeId);
            Mage::log('1 type id = '. $typeId);
            $this->_redirect('*/*/addselect');
            return;
        }

        if (!$this->_validateFormKey()) {
            Mage::log('contr: AddpostAction validate false ');
            $this->_redirect('*/*/add', array('type_id', $typeId));
            return ;
        }

        if ($this->getRequest()->isPost() && ($data = $this->getRequest()->getPost())) {
            Mage::log('addPostAction : isPost == TRUE ');
            Mage::log($data);

            $isError = false;

            try {
                Mage::log('Try: start');

                $model = Mage::getSingleton('enterprise_giftregistry/entity' );
                $model->setTypeId($typeId);
                $model->addData(array('attributes' => $this->getRequest()->getParam('attributes')));
                $model->addData(array(
                    'region' => $this->getRequest()->getParam('attributes'),
                    'event_date' => $this->getRequest()->getParam('event_date'),
                    'event_location' => $this->getRequest()->getParam('event_location'),
                    'country_id' => $this->getRequest()->getParam('country_id'),
                ));

                $model->addData(array(
                    'type_id' => $typeId,
                    'customer_id' => Mage::getSingleton('customer/session')->getCustomer()->getId(),
                    'website_id' => '0',
                    'is_public' => '1',
                    'url_key' => '/reg/1',
                    'title' => $this->getRequest()->getParam('title'),
                    'message' => $this->getRequest()->getParam('message'),
                    'shipping_address' => '12',
                    'custom_values' => serialize($this->getRequest()->getParam('attributes'))
                ));

                Mage::log('Try: set TypeID = '.$typeId);

                $addressType    = $this->getRequest()->getParam('address_type');
                switch ($addressType) {
                    case 'customer':
                        /* @var $customer Mage_Customer_Model_Customer */
                        $customer  = Mage::getSingleton('customer/session')->getCustomer();
                        $addressId = $this->getRequest()->getParam('shipping_address_id');
                        if (!$addressId) {
                            Mage::throwException('No address selected.');
                        }
                        $address = $customer->getAddressItemById($addressId);
                        if (!$address) {
                            Mage::throwException('Incorrect address selected.');
                        }
                        $model->importAddress($address);
                        break;
                    case 'new':
                        /* @var $address Mage_Customer_Model_Address */
                        $address = Mage::getModel('customer/address');
                        $address->setData($this->getRequest()->getParam('address'));
                        $errors = $address->validate();
                        if ($errors !== true) {
                            foreach ($errors as $err) {
                                $this->_getSession()->addError($err);
                            }
                            $isError = true;
                        }
                        break;
                    case 'none':
                    default:
                        break;

                }
//                $address = Mage::getModel('customer/address');
//
//                $address->
//                = $this->getRequest()->getParam('attributes');

                if (!$isError) {
                    $model->save();

                    $this->_getSession()->addSuccess(
                        Mage::helper('enterprise_giftregistry/data')->__('Data saved succesfully.')
                    );
                }
//                'static_registrant[]'
//                $_staticTypes = array('event_date', 'event_country_code', 'event_region_code', 'event_location');

//                Mage::log('Try: save ');
//                Mage::getSingleton('customer/session')->addSuccess($this->__('The gift registry type has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $isError = true;
//                $this->_redirect('*/*/add', array('type_id' => $typeId));
//                return;
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('enterprise_giftregistry/data')->__('Errors found.')
                );
                $isError = true;
//                Mage::getSingleton('customer/session')->addError($this->__('Failed to save gift registry type.'));
//                Mage::logException($e);
            }
            /*
            $customer = Mage::getModel('customer/customer')
                ->setId($this->_getSession()->getCustomerId())
                ->setWebsiteId($this->_getSession()->getCustomer()->getWebsiteId());

            $fields = Mage::getConfig()->getFieldset('customer_account');
            $data = $this->_filterPostData($this->getRequest()->getPost());

            foreach ($fields as $code=>$node) {
                if ($node->is('update') && isset($data[$code])) {
                    $customer->setData($code, $data[$code]);
                }
            }

            $errors = $customer->validate();
            if (!is_array($errors)) {
                $errors = array();
            }
*/
            if ($isError) {
                $this->_getSession()->setGiftRegistryEntityFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/*/edit');
                return $this;

                // set current data to sess
                // redirect to edit
            } else {
                $this->_redirect('*/*/');

                // redirect to next step
            }
        }
        Mage::log('Index ENDING ');
        $this->_redirect('*/*/');
    }

    /**
     * Return customer session instance
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    protected function _initEntity($requestParam = 'id')
    {
        $type = Mage::getModel('enterprise_giftregistry/type');
        $type->setStoreId($this->getRequest()->getParam('store', 0));

        if ($typeId = $this->getRequest()->getParam($requestParam)) {
            $type->load($typeId);
            if (!$type->getId()) {
                Mage::throwException($this->__('Wrong gift registry type requested.'));
            }
        }

        Mage::register('current_giftregistry_type', $type);
        return $type;
    }
}