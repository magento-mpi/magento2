<?php
/**
 * Admin ratings controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_RatingController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initEnityId();
        $this->loadLayout('baseframe');

        $this->_setActiveMenu('catalog/ratings');
        $this->_addBreadcrumb(__('Ratings'), __('Ratings'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/rating_rating'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_initEnityId();
        $this->loadLayout('baseframe');

        $this->_setActiveMenu('catalog/ratings');
        $this->_addBreadcrumb(__('Ratings'), __('Ratings'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/rating_edit'))
            ->_addLeft($this->getLayout()->createBlock('adminhtml/rating_edit_tabs'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        $this->_initEnityId();

        if ( $this->getRequest()->getPost() ) {
            try {
                $ratingModel = Mage::getModel('rating/rating');

                $ratingModel->setRatingCode($this->getRequest()->getParam('rating_code'))
                      ->setId($this->getRequest()->getParam('id'))
                      ->setEntityId(Mage::registry('entityId'))
                      ->save();

                $options = $this->getRequest()->getParam('option_title');

                if( is_array($options) ) {
                    $i = 1;
                    foreach( $options as $key => $optionCode ) {
                        $optionModel = Mage::getModel('rating/rating_option');
                        if( !preg_match("/^add_([0-9]*?)$/", $key) ) {
                            $optionModel->setId($key);
                        }

                        $optionModel->setCode($optionCode)
                            ->setValue($i)
                            ->setRatingId($ratingModel->getId())
                            ->save();
                        $i++;
                    }
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(__('Rating succesfully saved.'));
                Mage::getSingleton('adminhtml/session')->setRatingData(false);

                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setRatingData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                Mage::getModel('rating/rating')
                    ->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Rating succesfully deleted.'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    protected function _initEnityId()
    {
        Mage::register('entityId', Mage::getModel('rating/rating_entity')->getIdByCode('product'));
    }
}