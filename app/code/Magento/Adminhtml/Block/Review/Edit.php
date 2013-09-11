<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review edit form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Review;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'review';

        /** @var $actionPager \Magento\Review\Helper\Action\Pager */
        $actionPager = \Mage::helper('Magento\Review\Helper\Action\Pager');
        $actionPager->setStorageId('reviews');

        $reviewId = $this->getRequest()->getParam('id');
        $prevId = $actionPager->getPreviousItemId($reviewId);
        $nextId = $actionPager->getNextItemId($reviewId);
        if ($prevId !== false) {
            $this->addButton('previous', array(
                'label' => __('Previous'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/*', array('id' => $prevId)) . '\')'
            ), 3, 10);

            $this->addButton('save_and_previous', array(
                'label'   => __('Save and Previous'),
                'class'   => 'save',
                'data_attribute'  => array(
                    'mage-init' => array(
                        'button' => array(
                            'event' => 'save',
                            'target' => '#edit_form',
                            'eventData' => array(
                                'action' => array(
                                    'args' => array('next_item' => $prevId),
                                ),
                            ),
                        ),
                    ),
                ),
            ), 3, 11);
        }
        if ($nextId !== false) {
            $this->addButton('save_and_next', array(
                'label'   => __('Save and Next'),
                'class'   => 'save',
                'data_attribute'  => array(
                    'mage-init' => array(
                        'button' => array(
                            'event' => 'save',
                            'target' => '#edit_form',
                            'eventData' => array(
                                'action' => array(
                                    'args' => array('next_item' => $nextId),
                                ),
                            ),
                        ),
                    ),
                ),
            ), 3, 100);

            $this->addButton('next', array(
                'label' => __('Next'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/*', array('id' => $nextId)) . '\')'
            ), 3, 105);
        }
        $this->_updateButton('save', 'label', __('Save Review'));
        $this->_updateButton('save', 'id', 'save_button');
        $this->_updateButton('delete', 'label', __('Delete Review'));

        if( $this->getRequest()->getParam('productId', false) ) {
            $this->_updateButton(
                'back',
                'onclick',
                'setLocation(\''
                    . $this->getUrl(
                        '*/catalog_product/edit',
                        array('id' => $this->getRequest()->getParam('productId', false))
                    )
                    .'\')'
            );
        }

        if( $this->getRequest()->getParam('customerId', false) ) {
            $this->_updateButton(
                'back',
                'onclick',
                'setLocation(\''
                    . $this->getUrl(
                        '*/customer/edit',
                        array('id' => $this->getRequest()->getParam('customerId', false))
                    )
                    .'\')'
            );
        }

        if( $this->getRequest()->getParam('ret', false) == 'pending' ) {
            $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/*/pending') .'\')' );
            $this->_updateButton(
                'delete',
                'onclick',
                'deleteConfirm('
                    . '\'' . __('Are you sure you want to do this?').'\' '
                    . '\'' . $this->getUrl(
                        '*/*/delete',
                        array(
                            $this->_objectId => $this->getRequest()->getParam($this->_objectId),
                            'ret'           => 'pending',
                        )
                    ) . '\''
                    . ')'
            );
            \Mage::register('ret', 'pending');
        }

        if( $this->getRequest()->getParam($this->_objectId) ) {
            $reviewData = \Mage::getModel('Magento\Review\Model\Review')
                ->load($this->getRequest()->getParam($this->_objectId));
            \Mage::register('review_data', $reviewData);
        }

        $this->_formInitScripts[] = '
            var review = {
                updateRating: function() {
                        elements = [
                            $("select_stores"),
                            $("rating_detail").getElementsBySelector("input[type=\'radio\']")
                        ].flatten();
                        $(\'save_button\').disabled = true;
                        new Ajax.Updater(
                            "rating_detail",
                            "' . $this->getUrl('*/*/ratingItems', array('_current'=>true)).'",
                            {
                                parameters:Form.serializeElements(elements),
                                evalScripts:true,
                                onComplete:function(){ $(\'save_button\').disabled = false; }
                            }
                        );
                    }
           }
           Event.observe(window, \'load\', function(){
                 Event.observe($("select_stores"), \'change\', review.updateRating);
           });
        ';
    }

    public function getHeaderText()
    {
        if( \Mage::registry('review_data') && \Mage::registry('review_data')->getId() ) {
            return __("Edit Review '%1'", $this->escapeHtml(\Mage::registry('review_data')->getTitle()));
        } else {
            return __('New Review');
        }
    }
}
