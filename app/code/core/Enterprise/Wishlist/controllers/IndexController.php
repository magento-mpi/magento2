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
 * @package     Enterprise_Wishlist
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Multiple wishlist frontend search controller
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Check if multiple wishlist is enabled on current store before all other actions
     *
     * @return Enterprise_Wishlist_IndexController
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::helper('enterprise_wishlist')->isMultipleEnabled()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this;
        }

        if (!$this->_getSession()->getCustomerId() && !$this->_getSession()->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if (!$this->_getSession()->getBeforeWishlistUrl()) {
                $this->_getSession()->setBeforeWishlistUrl($this->_getRefererUrl());
            }
            $this->_getSession()->setBeforeWishlistRequest($this->getRequest()->getParams());
        }
        return $this;
    }

    /**
     * Retrieve customer session
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Load wishlist entity model by request argument
     *
     * @param string $requestParam
     * @return Mage_Wishlist_Model_Wishlist
     */
    protected function _getWishlist($requestParam = 'wishlist_id')
    {
        $wishlist = Mage::getModel('wishlist/wishlist');
        $customerId = $this->_getSession()->getCustomerId();
        $wishlistId = $this->getRequest()->getParam($requestParam);

        if ($wishlistId) {
            $wishlist->load($wishlistId);
        } else {
            $wishlist->loadByCustomer($customerId, true);
        }

        if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
            Mage::throwException(Mage::helper('enterprise_wishlist')->__('Wrong wishlist ID specified.'));
        }

        Mage::register('wishlist', $wishlist);
        return $wishlist;
    }

    /**
     * Create new customer wishlist
     */
    public function createwishlistAction()
    {
        $this->_forward('editwishlist');
    }

    /**
     * Edit wishlist properties
     *
     * @return Mage_Core_Controller_Varien_Action|Zend_Controller_Response_Abstract
     */
    public function editwishlistAction()
    {
        $customerId = $this->_getSession()->getCustomerId();
        $wishlistName = $this->getRequest()->getParam('name');
        $visibility = ($this->getRequest()->getParam('visibility', 0) === 'on' ? 1 : 0);
        $wishlistId = $this->getRequest()->getParam('wishlist_id');
        $wishlist = Mage::getModel('wishlist/wishlist');

        try {
            if (!$customerId) {
                Mage::throwException(Mage::helper('enterprise_wishlist')->__('Login to edit wishlsits.'));
            }
            if (!strlen($wishlistName)) {
                Mage::throwException(Mage::helper('enterprise_wishlist')->__('Provide wishlist name'));
            }
            if ($wishlistId){
                $wishlist->load($wishlistId);
                if ($wishlist->getCustomerId() !== $this->_getSession()->getCustomerId()) {
                    Mage::throwException(
                        Mage::helper('enterprise_wishlist')->__('You are not authorized to edit this wishlist')
                    );
                }
            } else {
                $wishlistCollection = Mage::getModel('wishlist/wishlist')->getCollection()
                    ->filterByCustomerId($customerId);
                $limit = Mage::helper('enterprise_wishlist')->getWishlistLimit();
                if (Mage::helper('enterprise_wishlist')->isWishlistLimitReached($wishlistCollection)) {
                    Mage::throwException(
                        Mage::helper('enterprise_wishlist')->__('You are alowed to create only %d wishlists', $limit)
                    );
                }
                $wishlist->setCustomerId($customerId);
            }
            $wishlist->setName($wishlistName)
                ->setVisibility($visibility)
                ->generateSharingCode()
                ->save();
            $this->_getSession()->addSuccess(
                Mage::helper('enterprise_wishlist')->__('Wishlist "%s" was successfully saved', Mage::helper('core')->escapeHtml($wishlist->getName()))
            );
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('enterprise_wishlist')->__('Error happened during wishlist creation')
            );
        }

        if (!$wishlist->getId()) {
            $this->_getSession()->addError('Could not create wishlist');
        }

        if ($this->getRequest()->isAjax()) {
            $this->getResponse()->setHeader('Content-Type', 'application/json');
            $params = array();
            if (!$wishlist->getId()) {
                $params = array('redirect' => Mage::getUrl('*/*'));
            } else {
                $params = array('wishlist_id' => $wishlist->getId());
            }
            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($params));
        } else {
            if (!$wishlist->getId()) {
                return $this->_redirect('*/*');
            } else {
                $this->_redirect('wishlist/index/index', array('wishlist_id' => $wishlist->getId()));
            }
        }
    }

    /**
     * Delete wishlist by id
     */
    public function deletewishlistAction()
    {
        try {
            $wishlist = $this->_getWishlist();
            if (Mage::helper('enterprise_wishlist')->isWishlistDefault($wishlist)) {
                Mage::throwException(
                    Mage::helper('enterprise_wishlist')->__('Default wishlist cannot be deleted')
                );
            }
            $wishlist->delete();
            Mage::helper('wishlist')->calculate();
            Mage::getSingleton('wishlist/session')->addSuccess(
                Mage::helper('enterprise_wishlist')->__('Wishlist "%s" has been deleted.', Mage::helper('core')->escapeHtml($wishlist->getName()))
            );
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $message = Mage::helper('enterprise_wishlist')->__('An error occurred while deleting wishlist.');
            $this->_getSession()->addException($e, $message);
        }
    }

    /**
     * Copy item to given wishlist
     *
     * @param int $itemId
     * @param Mage_Wishlist_Model_Wishlist $wishlist
     * @throws InvalidArgumentException|DomainException
     */
    protected function _copyItem($itemId, Mage_Wishlist_Model_Wishlist $wishlist)
    {
        /* @var Mage_Wishlist_Model_Item $item */
        $item = Mage::getModel('wishlist/item');
        $item->loadWithOptions($itemId);
        if (!$item->getId()) {
            throw new InvalidArgumentException();
        }
        if ($item->getWishlistId() == $wishlist->getId()) {
            throw new DomainException();
        }
        $wishlist->addNewItem($item->getProduct(), $item->getBuyRequest());
        Mage::dispatchEvent(
            'wishlist_add_product',
            array(
                'wishlist'  => $wishlist,
                'product'   => $item->getProduct(),
                'item'      => $item
            )
        );
    }

    /**
     * Copy wishlist item to given wishlist
     *
     * @return void
     */
    public function copyitemAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }
        $itemId = $this->getRequest()->getParam('item_id');
        if ($itemId) {
            try {
                $this->_copyItem($itemId, $wishlist);
                $name = Mage::helper('core')->escapeHtml($wishlist->getName());
                $this->_getSession()->addSuccess(
                    Mage::helper('enterprise_wishlist')->__('Item was successfully copied to wishlist "%s"', $name)
                );
                Mage::helper('wishlist')->calculate();
            } catch (InvalidArgumentException $e) {
                $this->_getSession->addError(
                    Mage::helper('enterprise_wishlist')->__('Item not found')
                );
            } catch (DomainException $e) {
                $this->_getSession()->addError(
                    Mage::helper('enterprise_wishlist')->__('The item is present in selected wishlist')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError(
                    Mage::helper('enterprise_wishlist')->__('Could not copy wishlist item')
                );
            }
        }
        $wishlist->save();
        $this->_redirectReferer();
    }

    /**
     * Copy wishlist items to given wishlist
     *
     * @return void
     */
    public function copyitemsAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }
        $itemIds = $this->getRequest()->getParam('selected', array());
        $notFound = array();
        $alreadyPresent = array();
        $failed = array();
        $copied = array();
        if (count($itemIds)) {
            foreach ($itemIds as $id => $value) {
                try {
                    $this->_copyItem($id, $wishlist);
                    $copied[] = $id;
                } catch (InvalidArgumentException $e) {
                    $notFound[] = $id;
                } catch (DomainException $e) {
                    $alreadyPresent[] = $id;
                } catch (Exception $e) {
                    Mage::logException($e);
                    $failed[] = $id;
                }
            }
        }
        $wishlist->save();

        if (count($notFound)) {
            $this->_getSession()->addError(
                Mage::helper('enterprise_wishlist')->__('Some items were not found')
            );
        }

        if (count($failed)) {
            $this->_getSession()->addError(
                Mage::helper('enterprise_wishlist')->__('Some items could not be copied')
            );
        }

        if (count($alreadyPresent)) {
            $this->_getSession()->addError(
                Mage::helper('enterprise_wishlist')->__('Some items are present in selected wishlist')
            );
        }

        if (count($copied)) {
            Mage::helper('wishlist')->calculate();
            $this->_getSession()->addSuccess(
                Mage::helper('enterprise_wishlist')->__('Items were copied to wishlist "%s"', Mage::helper('core')->escapeHtml($wishlist->getName()))
            );
        }
        $this->_redirectReferer();
    }

    /**
     * Move item to given wishlist.
     * Check whether item belongs to one of customer's wishlists
     *
     * @param $itemId
     * @param Mage_Wishlist_Model_Wishlist $wishlist
     * @param Mage_Wishlist_Model_Resource_Wishlist_Collection $customerWishlists
     * @throws InvalidArgumentException|DomainException
     */
    protected function _moveItem(
        $itemId,
        Mage_Wishlist_Model_Wishlist $wishlist,
        Mage_Wishlist_Model_Resource_Wishlist_Collection $customerWishlists
    ) {
        /* @var Mage_Wishlist_Model_Item $item */
        $item = Mage::getModel('wishlist/item');
        $item->loadWithOptions($itemId);
        if (!$item->getId()) {
            throw new InvalidArgumentException();
        }
        if ($item->getWishlistId() == $wishlist->getId()) {
            throw new DomainException(null, 1);
        }
        if (!$customerWishlists->getItemById($item->getWishlistId())) {
            throw new DomainException(null, 2);
        }
        $wishlist->addNewItem($item->getProduct(), $item->getBuyRequest());
        $item->delete();
    }

    /**
     * Move wishlist item to given wishlist
     *
     * @return void
     */
    public function moveitemAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }
        $itemId = $this->getRequest()->getParam('item_id');

        if ($itemId) {
            try {
                $wishlists = Mage::getModel('wishlist/wishlist')->getCollection()
                    ->filterByCustomerId($this->_getSession()->getCustomerId());

                $this->_moveItem($itemId, $wishlist, $wishlists);
                $name = Mage::helper('core')->escapeHtml($wishlist->getName());
                $this->_getSession()->addSuccess(
                    Mage::helper('enterprise_wishlist')->__('Item was successfully moved to wishlist "%s"', $name)
                );
                Mage::helper('wishlist')->calculate();
            } catch (InvalidArgumentException $e) {
                $this->_getSession()->addError(
                    Mage::helper('enterprise_wishlist')->__("Item with given id doesn't exist")
                );
            } catch (DomainException $e) {
                if ($e->getCode() == 1) {
                    $this->_getSession()->addError(
                        Mage::helper('enterprise_wishlist')->__('Item is already in given wishlist')
                    );
                } else {
                    $this->_getSession()->addError(
                        Mage::helper('enterprise_wishlist')->__('Item cannot be moved')
                    );
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('enterprise_wishlist')->__('Could not move wishlist item')
                );
            }
        }
        $wishlist->save();
        $this->_redirectReferer();
    }

    /**
     * Move wishlist items to given wishlist
     */
    public function moveitemsAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }
        $itemIds = $this->getRequest()->getParam('selected', array());
        $moved = array();
        $failed = array();
        $notFound = array();
        $notAllowed = array();
        $alreadyPresent = array();
        if (count($itemIds)) {
            $wishlists = Mage::getModel('wishlist/wishlist')->getCollection();
            $wishlists->filterByCustomerId($this->_getSession()->getCustomerId());

            foreach ($itemIds as $id => $value) {
                try {
                    $this->_moveItem($id, $wishlist, $wishlists);
                    $moved[] = $id;
                } catch (InvalidArgumentException $e) {
                    $notFound[] = $id;
                } catch (DomainException $e) {
                    if ($e->getCode() == 1) {
                        $alreadyPresent[] = $id;
                    } else {
                        $notAllowed[] = $id;
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                    $failed[] = $id;
                }
            }
        }
        if (count($notFound)) {
            $this->_getSession()->addError(
                Mage::helper('enterprise_wishlist')->__('Some items were not found')
            );
        }

        if (count($notAllowed)) {
            $this->_getSession()->addError(
                Mage::helper('enterprise_wishlist')->__('Some items were not allowed to be moved')
            );
        }

        if (count($alreadyPresent)) {
            $this->_getSession()->addError(
                Mage::helper('enterprise_wishlist')->__('Some items are present in selected wishlist')
            );
        }

        if (count($failed)) {
            $this->_getSession()->addError(
                Mage::helper('enterprise_wishlist')->__('Some items could not be moved')
            );
        }

        if (count($moved)) {
            Mage::helper('wishlist')->calculate();
            $this->_getSession()->addSuccess(
                Mage::helper('enterprise_wishlist')->__('Items were succesfully moved to wishlist "%s"', Mage::helper('core')->escapeHtml($wishlist->getName()))
            );
        }
        $this->_redirectReferer();
    }
}
