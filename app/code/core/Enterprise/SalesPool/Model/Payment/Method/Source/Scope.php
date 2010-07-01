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
 * @package     Enterprise_SalesPool
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Payment methods scope source
 *
 */
class Enterprise_SalesPool_Model_Payment_Method_Source_Scope
{
    /**
     * Retrieve scope options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array('label' => Mage::helper('enterprise_salespool')->__('All Allowed Payment Methods'), 'value' => 0),
            array('label' => Mage::helper('enterprise_salespool')->__('Specific Payment Methods'), 'value' => 1)
        );

        return $options;
    }
}
