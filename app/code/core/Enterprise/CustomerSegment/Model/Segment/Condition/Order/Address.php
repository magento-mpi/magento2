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
 * @package    Enterprise_CustomerSegment
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_CustomerSegment_Model_Segment_Condition_Order_Address
    extends Enterprise_CustomerSegment_Model_Condition_Combine_Abstract
{
    protected $_inputType = 'select';

    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_order_address');
    }

    public function getNewChildSelectOptions()
    {
        return Mage::getModel('enterprise_customersegment/segment_condition_order_address_combine')
            ->getNewChildSelectOptions();
    }

    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('enterprise_customersegment')->__('If Order Addresses match %s of these Conditions:',
                $this->getAggregatorElement()->getHtml()) . $this->getRemoveLinkHtml();
    }
}
