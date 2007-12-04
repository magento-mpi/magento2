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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Gift Message Helper Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Giftmessage_Helper extends Mage_Adminhtml_Block_Widget
{
    protected $_entity = null;
    protected $_type   = null;
    static protected $_scriptIncluded = false;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('giftmessage/helper.phtml');
    }

    public function setEntity($entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    public function getEntity()
    {
        return $this->_entity;
    }

    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function hasGiftMessage()
    {
        return $this->getEntity()->getGiftMessageId() > 0;
    }

    public function getMessage()
    {
        return $this->helper('giftmessage/message')->getGiftMessage($this->getEntity()->getGiftMessageId());
    }

    public function setScriptIncluded($value)
    {
        self::$_scriptIncluded = $value;
        return $this;
    }

    public function getScriptIncluded()
    {
        return self::$_scriptIncluded;
    }

    public function getJsObjectName()
    {
        return $this->getId() . 'JsObject';
    }

    public function getEditUrl()
    {
        return $this->helper('giftmessage/url')->getAdminEditUrl($this->getEntity(), $this->getType());
    }

    public function prepareAsIs($text)
    {
        return nl2br($this->htmlEscape($text));
    }

    public function getWidgetButtonHtml($label, $additionalCss='')
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->addData(array(
                'label'=>$label,
                'type'=>'button',
                'class'=>'listen-for-click ' . $additionalCss
            ))->toHtml();
    }
} // Class Mage_Adminhtml_Block_GiftMessage_Helper End