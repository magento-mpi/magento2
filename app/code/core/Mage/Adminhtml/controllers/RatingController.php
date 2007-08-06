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
        if ( $this->getRequest()->getPost() ) {
            try {
                $ratingModel = Mage::getModel('rating/rating');

                if( !$this->getRequest()->getParam('id') ) {
                    $ratingModel->setDatePosted(now());
                }

                $ratingModel->setRatingCode($this->getRequest()->getParam('rating_code'))
                      ->setId($this->getRequest()->getParam('id'))
                      ->save();

                $options = $this->getRequest()->getParam('option');

                if( is_array($options) ) {
                    foreach( $options as $key => $option ) {
                        $optionModel = Mage::getModel('rating/rating_option');
                        if( intval($key) > 0 ) {
                            $optionModel->setId($key);
                        }
                        $optionModel->setCode($option['code'])
                            ->setValue($option['value'])
                            ->setRatingId($ratingModel->getId())
                            ->save();
                    }
                }

                $optionsDelete = $this->getRequest()->getParam('deleteOption');
                if( is_array($optionsDelete) ) {
                    foreach( $optionsDelete as $option ) {
                        $optionModel = Mage::getModel('rating/rating_option');
                        $optionModel->setId($option)
                            ->delete();
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

    protected function _initEnityId()
    {
        Mage::register('entityId', Mage::getModel('rating/rating_entity')->getIdByCode('product'));
    }
}