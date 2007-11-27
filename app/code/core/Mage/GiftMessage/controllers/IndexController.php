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
 * @category   Mage
 * @package    Mage_GiftMessage
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Gift Messages index controller
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_GiftMessage_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        /* Do nothing */
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction()
    {
        $giftMessage = Mage::getModel('giftmessage/message');
        if($this->getRequest()->getParam('message')) {
            $giftMessage->load($this->getRequest()->getParam('message'));
        }
        try {
            $entity = $giftMessage->getEntityModelByType($this->_getMappedType($this->getRequest()->getParam('type')));

            $giftMessage->setSender($this->getRequest()->getParam('sender'))
                ->setRecipient($this->getRequest()->getParam('recipient'))
                ->setMessage($this->getRequest()->getParam('messagetext'))
                ->save();


            $entity->load($this->getRequest()->getParam('item'))
                ->setGiftMessageId($giftMessage->getId())
                ->save();

            $this->getRequest()->setParam('message', $giftMessage->getId());
            $this->getRequest()->setParam('entity', $entity);
        } catch (Exception $e) {

        }

        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _getMappedType($type)
    {
        $map = array(
            'main'          =>  'quote',
            'item'          =>  'quote_item',
            'address'       =>  'quote_address',
            'address_item'  =>  'quote_address_item'
        );

        if (isset($map[$type])) {
            return $map[$type];
        }

        return null;
    }

    protected function buttonAction()
    {
        $giftMessage = Mage::getModel('giftmessage/message');
        $entity = $giftMessage->getEntityModelByType($this->_getMappedType($this->getParam('type')));
        $entity->load($this->getRequest()->getParam('item'));
        $this->getResponse()->setBody($this->getLayout()->createBlock('giftmessage/message_helper')
                                        ->setEntity($entity)
                                        ->setType($type)->toHtml().'123123');
    }

} // Class Mage_GiftMessage_IndexController End