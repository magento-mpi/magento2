<?php
/**
 * Wishlist front controller
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 * @author	   Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Wishlist_IndexController extends Mage_Core_Controller_Front_Action
{
	public function preDispatch()
	{
		parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            Mage::getSingleton('customer/session')->setBeforeWishlistUrl($this->getRequest()->getServer('HTTP_REFERER'));
            $this->setFlag('', 'no-dispatch', true);
        }
	}

	public function indexAction()
	{
		try {
			$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
		}
		catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->addError('Cannot create wishlist');
		}

		Mage::register('wishlist', $wishlist);


		$this->loadLayout(array('default', 'customer_account'), 'customer_account');

		$this->_initLayoutMessages('customer/session');
		$this->getLayout()->getBlock('content')
			->append($this->getLayout()->createBlock('wishlist/customer_wishlist','customer.wishlist'));
		$this->renderLayout();
	}

	public function addAction()
	{
		try {
			$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
		}
		catch (Exception $e) {
			Mage::getSingleton('customer/session')->addError('Cannot create wishlist');
			$this->_redirect('*');
			return;
		}

		$productId = (int) $this->getRequest()->getParam('product');
		$product = Mage::getModel('catalog/product')->load($productId);
		if (!$product->getId()) {
		    Mage::getSingleton('customer/session')->addError('Can not specify product');
		    $this->_redirect('*');
		    return;
		}

		try {
			$wishlist->addNewItem($product->getId());
			$message = $product->getName().' was successfully added to your wishlist. Click <a href="%s">here</a> to continue shopping';

			if ($referer = Mage::getSingleton('customer/session')->getBeforeWishlistUrl()) {
			    Mage::getSingleton('customer/session')->setBeforeWishlistUrl(null);
			}
			else {
			    $referer = $this->getRequest()->getServer('HTTP_REFERER');
			}
			$message = sprintf($message, $referer);
			Mage::getSingleton('customer/session')->addSuccess($message);
		}
		catch (Exception $e) {
			Mage::getSingleton('customer/session')->addError($e->getMessage());
		}

		$this->_redirect('*');
	}

	public function updateAction()
	{
		$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);

		if($post = $this->getRequest()->getPost()) {
			foreach ($post['description'] as $itemId => $description) {
				$item = Mage::getModel('wishlist/item')->load($itemId);
				if($item->getWishlistId()!=$wishlist->getId()) {
					continue;
				}

				try {
	               	$item->setDescription($description)
	               		->save();
                }
                catch (Exception $e) { }
			}
		}


		$this->_redirect('*');
	}

	public function removeAction() {
		$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
		$id = (int) $this->getRequest()->getParam('item');
		$item = Mage::getModel('wishlist/item')->load($id);

		if($item->getWishlistId()==$wishlist->getId()) {
			try {
				$item->delete();
			}
			catch(Exception $e) {
				Mage::getSingleton('customer/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*');
	}

	public function cartAction() {
		$wishlist = Mage::getModel('wishlist/wishlist')
            ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);

		$id = (int) $this->getRequest()->getParam('item');
		$item = Mage::getModel('wishlist/item')->load($id);

		if($item->getWishlistId()==$wishlist->getId()) {
			 try {
	            $product = Mage::getModel('catalog/product')->load($item->getProductId())->setQty(1);
	            $quote = Mage::getSingleton('checkout/session')->getQuote();
	            $quote->addCatalogProduct($product)->save();
            	$item->delete();
            }
			catch(Exception $e) {
				Mage::getSingleton('customer/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('checkout/cart');
	}

	public function allcartAction() {
		$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
		$quote = Mage::getSingleton('checkout/session')->getQuote();

		$wishlist->getItemCollection()->load();
		foreach ($wishlist->getItemCollection() as $item) {
 			 try {
	            $product = Mage::getModel('catalog/product')->load($item->getProductId())->setQty(1);
	            $quote->addCatalogProduct($product)->save();
            	$item->delete();
            }
			catch(Exception $e) {
				Mage::getSingleton('customer/session')->addError($e->getMessage());
			}
		}

		$this->_redirect('checkout/cart');
	}

	public function shareAction()
	{
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->getLayout()->getBlock('content')
			->append($this->getLayout()->createBlock('wishlist/customer_sharing','wishlist.sharing'));
		$this->renderLayout();
	}

	public function sendAction()
	{
		try{
			if(!$this->getRequest()->getParam('email')) {
				Mage::throwException('E-mail Addresses required', 'wishlist/session');
			}

			$emails = explode(',', $this->getRequest()->getParam('email'));

			$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
			Mage::register('wishlist', $wishlist);

			$message = nl2br(htmlspecialchars($this->getRequest()->getParam('message')));

			$wishlistBlock = $this->getLayout()->createBlock('wishlist/share_email_items')->toHtml();

			foreach($emails as $key => $email) {
				$email = trim($email);
				$emails[$key] = $email;
			}

			$emails = array_unique($emails);

            $emailModel = Mage::getModel('core/email_template');
			foreach($emails as $email) {
        		$emailModel->sendTransactional(
        		    Mage::getStoreConfig('wishlist/email/email_template'),
        		    Mage::getStoreConfig('wishlist/email/email_identity'),
                    $email,
                    null,
				    array(
						'items'		 		=> &$wishlistBlock,
						'addAllLink' 		=> Mage::getUrl('*/shared/allcart',array('code'=>$wishlist->getSharingCode())),
						'viewOnSiteLink'	=> Mage::getUrl('*/shared/index',array('code'=>$wishlist->getSharingCode())),
						'message'			=> $message
					));
			}

			$wishlist->setShared(1);
			$wishlist->save();
			Mage::getSingleton('customer/session')->addSuccess('Your Wishlist successfully shared');
			$this->_redirect('*/*');
		}
		catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->setData('sharing_form', $this->getRequest()->getParams());
			$this->_redirect('*/*/share');
		}
	}
}// Class Mage_Wishlist_IndexController END
