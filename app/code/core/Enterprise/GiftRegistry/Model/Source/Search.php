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
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Search source model
 */
class Enterprise_GiftRegistry_Model_Source_Search
{
    /**
     * Quick search form types
     */
    const SEARCH_ALL_FORM   = 'all';
    const SEARCH_TYPE_FORM  = 'type';
    const SEARCH_EMAIL_FORM = 'email';
    const SEARCH_ID_FORM    = 'id';

    /**
     * Return form types as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::SEARCH_ALL_FORM,
                'label' => Mage::helper('enterprise_giftregistry')->__('All Forms')
            ),
            array(
                'value' => self::SEARCH_TYPE_FORM,
                'label' => Mage::helper('enterprise_giftregistry')->__('Recipient Name/Registry Type Search')
            ),
            array(
                'value' => self::SEARCH_EMAIL_FORM,
                'label' => Mage::helper('enterprise_giftregistry')->__('Recipient Email Search')
            ),
            array(
                'value' => self::SEARCH_ID_FORM,
                'label' => Mage::helper('enterprise_giftregistry')->__('Gift Registry ID Search')
            )
        );
    }
}
