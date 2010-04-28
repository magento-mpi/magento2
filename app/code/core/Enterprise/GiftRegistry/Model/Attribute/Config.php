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
 * Gift registry custom attribute config model
 */
class Enterprise_GiftRegistry_Model_Attribute_Config extends Enterprise_Enterprise_Model_Core_Abstract
{
    /**
     * Path to types of available custom attributes
     */
    const XML_ATTRIBUTE_TYPES_PATH = 'global/attributes/groups';

    /**
     * Path to attribute groups
     */
    const XML_ATTRIBUTE_GROUPS_PATH = 'global/prototype/registry/attribute_groups';


    /**
     * Return array of default options
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return array(array(
            'value' => '',
            'label' => Mage::helper('enterprise_giftregistry')->__('-- Please select --'))
        );
    }

    /**
     * Return array of attribute type renderers
     *
     * @return array
     */
    public function getAttributeRenderers()
    {
        $renderers = array();
        $groups = Mage::getConfig()->getNode(self::XML_ATTRIBUTE_TYPES_PATH)->children();

        foreach ($groups as $group) {
            $typesPath = implode('/', array(self::XML_ATTRIBUTE_TYPES_PATH, $group->getName(), 'types'));
            foreach (Mage::getConfig()->getNode($typesPath)->children() as $type) {
                $path = implode('/', array($typesPath, $type->getName(), 'render'));
                if ($renderer = (string)Mage::getConfig()->getNode($path)) {
                    $renderers[] = $renderer;
                }
            }
        }
        return $renderers;
    }

    /**
     * Return array of attribute types for using as options
     *
     * @return array
     */
    public function getAttributeTypesOptions()
    {
        $options = $this->getDefaultOptions();
        $groups = Mage::getConfig()->getNode(self::XML_ATTRIBUTE_TYPES_PATH)->children();

        foreach ($groups as $group) {
            $types = array();
            $typesPath = implode('/', array(self::XML_ATTRIBUTE_TYPES_PATH, $group->getName(), 'types'));
            foreach (Mage::getConfig()->getNode($typesPath)->children() as $type) {
                $labelPath = implode('/', array($typesPath, $type->getName(), 'label'));
                $types[] = array(
                    'label' => (string) Mage::getConfig()->getNode($labelPath),
                    'value' => $type->getName()
                );
            }
            $labelPath = implode('/', array(self::XML_ATTRIBUTE_TYPES_PATH, $group->getName(), 'label'));
            $options[] = array(
                'label' => (string) Mage::getConfig()->getNode($labelPath),
                'value' => $types
            );
        }
        return $options;
    }

    /**
     * Return array of attribute groups for using as options
     *
     * @return array
     */
    public function getAttributeGroupsOptions()
    {
        $options = $this->getDefaultOptions();
        $groups  = Mage::getConfig()->getNode(self::XML_ATTRIBUTE_GROUPS_PATH)->children();

        foreach ($groups as $group) {
            $path = implode('/', array(self::XML_ATTRIBUTE_GROUPS_PATH, $group->getName(), 'label'));
            $options[] = array(
                'value' => $group->getName(),
                'label' => (string) Mage::getConfig()->getNode($path)
            );
        }
        return $options;
    }

}
