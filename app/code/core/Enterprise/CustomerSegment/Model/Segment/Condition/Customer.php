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
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Customer conditions options group
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Customer
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Customer');
        $this->setValue(null);
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = array();
        $prefix = 'Enterprise_CustomerSegment_Model_Segment_Condition_Customer_';
        $conditions = Mage::getModel($prefix.'Attributes')->getNewChildSelectOptions();
        $conditions = array_merge($conditions, Mage::getModel($prefix.'Newsletter')->getNewChildSelectOptions());
        $conditions = array_merge($conditions, Mage::getModel($prefix.'Storecredit')->getNewChildSelectOptions());
        return array(
            'value' => $conditions,
            'label'=>Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Customer')
        );
    }
}
