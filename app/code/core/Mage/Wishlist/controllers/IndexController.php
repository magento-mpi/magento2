<?php
/**
 * Wishlist front controller
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_IndexController extends Mage_Core_Controller_Front_Action
{
	public function preDispatch()
	{
		parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
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

		$this->_initLayoutMessages('wishlist/session');
		$this->getLayout()->getBlock('content')
			->append(
				$this->getLayout()->createBlock('wishlist/customer_wishlist','customer.wishlist')
			);
		$this->renderLayout();
	}

	public function addAction()
	{
		try {
			$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
		}
		catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->addError('Cannot create wishlist');
		}

		try {
			$wishlist->addNewItem($this->getRequest()->getParam('product'));
			Mage::getSingleton('wishlist/session')->addSuccess('Product successfully added to wishlist');
		}
		catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->addError($e->getMessage());
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
				Mage::getSingleton('wishlist/session')->addError($e->getMessage());
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
				Mage::getSingleton('wishlist/session')->addError($e->getMessage());
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
				Mage::getSingleton('wishlist/session')->addError($e->getMessage());
			}
		}

		$this->_redirect('checkout/cart');
	}

	public function shareAction()
	{
		$this->loadLayout();
		$this->_initLayoutMessages('wishlist/session');
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

			$template = Mage::getModel('core/email_template')
				->load(Mage::getStoreConfig('email/templates/wishlist_share_message'));
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
			
			foreach($emails as $email) {
				$template->send($email,
					array(
						'items'		 		=> &$wishlistBlock,
						'addAllLink' 		=> Mage::getUrl('*/shared/tocart',array('code'=>$wishlist->getSharingCode())),
						'viewOnSiteLink'	=> Mage::getUrl('*/shared/index',array('code'=>$wishlist->getSharingCode())),
						'message'			=> $message
					)
				);
			}

			$wishlist->setShared(1);
			$wishlist->save();
			Mage::getSingleton('wishlist/session')->addSuccess('Your Wishlist successfully shared');
			$this->_redirect('*/*');
		}
		catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->setData('sharing_form', $this->getRequest()->getParams());
			$this->_redirect('*/*/share');
		}
	}

/*
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getConfig()->getModuleConfig('Mage_Customer')->is('wishlistActive')) {
            $this->getResponse()->setRedirect('noRoute');
        }

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    public function indexAction()
    {
        $this->loadLayout();

        $collection = Mage::getSingleton('customer/session')->getCustomer()
            ->getWishlistCollection();

        $collection->getProductCollection()
            ->addAttributeToSelect('name');
        $collection->load();

        $block = $this->getLayout()->createBlock('core/template', 'wishlist')
            ->setTemplate('customer/wishlist.phtml')
            ->assign('wishlist', $collection);
        $this->getLayout()->getBlock('content')->append($block);

        $this->renderLayout();
    }

    public function updatePostAction()
    {
        $p = $this->getRequest()->getPost();
        if (!empty($p['wishlist'])) {
            foreach ($p['wishlist'] as $itemId=>$dummy) {
                if (isset($p['to_cart'][$itemId])) {
                    $wishlist = Mage::getModel('customer/wishlist')->load($itemId);

                    $product = Mage::getModel('catalog/product')->load($wishlist->getProductId())->setQty(1);

                    $quote = Mage::getSingleton('checkout/session')->getQuote();
                    $quote->addProduct($product)->save();

                    $wishlist->delete();
                }
                if (isset($p['remove'][$itemId])) {
                    $wishlist = Mage::getModel('customer/wishlist')->load($itemId);
                    $wishlist->delete();
                }
            }
            if (isset($p['to_cart'])) {
                $this->_redirect('checkout/cart');
                return;
            }
        }
        $this->_redirect('customer/wishlist');
    }

    public function addAction()
    {
        $productId = $this->getRequest()->getParam('product');
        try {
            Mage::getModel('customer/wishlist')->setProductId($productId)->save();
        }
        catch (Exception $e){

        }
        if (false && $url = $this->getRequest()->getServer('HTTP_REFERER')) {
            $this->getResponse()->setRedirect($url);
            return;
        }
        $this->_redirect('customer/wishlist');
    }
*/

}// Class Mage_Wishlist_IndexController END
