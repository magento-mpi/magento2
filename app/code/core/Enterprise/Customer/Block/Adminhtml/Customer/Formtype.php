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
 * @category   Enterprise
 * @package    Enterprise_Customer
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Form Types Grid Container Block
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Formtype extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     *
     */
    public function __construct()
    {
        $this->_blockGroup = 'enterprise_customer';
        $this->_controller = 'adminhtml_customer_formtype';
        $this->_headerText = Mage::helper('enterprise_customer')->__('Manage Form Types');

        parent::__construct();

        $this->_updateButton('add', 'label', Mage::helper('enterprise_customer')->__('New Form Type'));
    }
}
