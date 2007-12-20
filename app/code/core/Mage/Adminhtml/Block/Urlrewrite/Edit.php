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
 * Adminhtml sales order edit
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Kyaw Soe Lynn Maung <vincent@varien.com>
 */

class Mage_Adminhtml_Block_Urlrewrite_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'urlrewrite';

        parent::__construct();

        $this->_updateButton('save', 'label', __('Save Url'));
        $this->_updateButton('delete', 'label', __('Delete Tag'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('urlrewrite_urlrewrite')->getId()) { // TOCHECK
            return __('Edit Url #%s', Mage::registry('urlrewrite_urlrewrite')->getId());
        }
        else {
            return __('New Url');
        }
    }

//    public function getBackUrl()
//    {
//        return Mage::getUrl('*/sales_order/view', array('order_id' => Mage::registry('sales_order')->getId()));
//    }

}