<?php
/**
 * Tag Frontend controller
 *
 * @package     Mage
 * @subpackage  Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tag_IndexController extends Mage_Core_Controller_Front_Action
{
    public function saveAction()
    {
        if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
            $this->getResponse()->setRedirect($referer);
        }

        if( $tagName = $this->getRequest()->getQuery('tagName') ) {
            try {
                if( !Mage::getSingleton('customer/session')->authenticate($this) ) {
                    return;
                }

                $customerId = Mage::getSingleton('customer/session')->getCustomerId();

                $tagName = urldecode($tagName);
                $tagNamesArr = explode("\n", preg_replace("/(\'(.*?)\')|(\s+)/i", "$1\n", $tagName));

                foreach( $tagNamesArr as $key => $tagName ) {
                    $tagNamesArr[$key] = trim($tagNamesArr[$key]);
                    if( $tagNamesArr[$key] == '' ) {
                        unset($tagNamesArr[$key]);
                    }
                }

                foreach( $tagNamesArr as $tagName ) {
                    $tagName = trim($tagName, '\'');
                    if( $tagName ) {
                        $tagModel = Mage::getModel('tag/tag');
                        $tagModel->loadByName($tagName);

                        $tagModel->setName($tagName)
                                ->setStoreId(Mage::getSingleton('core/store')->getId())
                                ->setStatus( ( $tagModel->getId() && $tagModel->getStatus() != $tagModel->getPendingStatus() ) ? $tagModel->getStatus() : $tagModel->getPendingStatus() )
                                ->save();

                        $tagRelationModel = Mage::getModel('tag/tag_relation');

                        $tagRelationModel->loadByTagCustomer($this->getRequest()->getParam('productId'), $tagModel->getId(), Mage::getSingleton('customer/session')->getCustomerId());

                        if( $tagRelationModel->getCustomerId() == $customerId ) {
                            return;
                        }

                        $tagRelationModel->setTagId($tagModel->getId())
                            ->setCustomerId($customerId)
                            ->setProductId($this->getRequest()->getParam('productId'))
                            ->setStoreId(Mage::getSingleton('core/store')->getId())
                            ->setCreatedAt( now() )
                            ->save();
                    } else {
                        continue;
                    }
                }

                Mage::getSingleton('catalog/session')
                        ->addSuccess('Your tag(s) have been submitted.');

                $product = Mage::getModel('catalog/product')
                    ->load($this->getRequest()->getParam('productId'));
                $productUrl = $product->getProductUrl();

                $this->getResponse()->setRedirect($productUrl);
                return;
            } catch (Exception $e) {
                Mage::getSingleton('catalog/session')
                    ->addError('Unable to save tag(s).');
                return;
            }
        }
    }
    /*
    public function saveAllAction()
    {
        $tagName = $this->getRequest()->getParam('tagName');
        $tagNamesArr = explode("\n", preg_replace("/'*(\d+)(\s+)/i", "$1\n", $tagName));

        for($i=0;$i<=500;$i++) {
            $tagName = rand(1100, 1150);
            if( $tagName ) {
                $tagModel = Mage::getModel('tag/tag');
                $tagModel->loadByName($tagName);

                $tagModel->setName($tagName)
                        ->setStoreId(1)
                        ->setStatus( $tagModel->getApprovedStatus() )
                        ->save();

                $tagRelationModel = Mage::getModel('tag/tag_relation');

                $tagRelationModel->setTagId($tagModel->getId())
                    ->setCustomerId(44)
                    ->setProductId(2)
                    ->setStoreId(1)
                    ->save();
            }
        }
    }
    */
}