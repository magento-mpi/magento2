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
 * Customer giftregistry edit block
 *
 * @category   Enterprise
 * @package    Enterprise_GiftRegistry
 */
class Enterprise_GiftRegistry_Block_Customer_Edit_Registry extends  Enterprise_GiftRegistry_Block_Customer_Edit_Abstract
{
    /**
     * Scope Selector 'registry/registrant'
     *
     * @var string
     */
    protected $_prefix = 'registry';

    /**
     * Static types list
     *
     * @var array
     */
    protected $_staticTypes = array(
        'event_date', 'event_country_code', 'event_region', 'event_region_text', 'event_location');

    /**
     * Holds fields which has to be setted up by javascript
     *
     * @var array
     */
    protected $_staticAttributes = array(
        'is_public', 'event_region', 'event_region_text' , 'event_date', 'event_location', 'event_country_code');

    /**
     * Return array of attributes groupped by group
     *
     * @return array
     */
    public function getGroupedRegistryAttributes()
    {
        return $this->getGroupedAttributes();
    }

    /**
     * Return privacy field selector (input type = select)
     *
     * @return sting
     */
    public function getIsPublicHtml()
    {
        $options[''] = Mage::helper('enterprise_giftregistry')->__('Please Select');
        $options += $this->getEntity()->getOptionsIsPublic();
        return $this->getSelectHtml($options, 'is_public', 'is_public', $this->getIsPublic());
    }

    /**
     * Getter for static attributes fields
     */
    public function getStaticAttributes()
    {
        return $this->_staticAttributes;
    }

    /**
     * Wrap field id by scope
     *
     * @param string id     - DOM element id
     * @return string
     */
    public function wrapId($id) {
        if (!in_array($id, $this->_staticTypes)) {
            $id = $this->_prefix . ':' . $id;
        }
        return $id;
    }
}
