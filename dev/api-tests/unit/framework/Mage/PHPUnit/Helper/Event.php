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
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Helper class for observer's events.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Helper_Event extends Mage_PHPUnit_Helper_Abstract
{
    /**
     * Remove event observers from config
     *
     * @param array $eventNames - remove observers for events from this array. if array is empty, remove for all events
     * @return Mage_PHPUnit_Helper_Event
     */
    public function disableObservers($eventNames = array())
    {
        $events = array();
        foreach ($eventNames as $name) {
            $events[] = "name() = '{$name}'";
        }
        $query = !empty($events) ? "[" . implode(' or ', $events) . "]" : "";

        $elements = Mage::getConfig()->getXpath("//*/events/*" . $query);
        foreach ($elements as $element) {
            $element->setNode('observers', null);
        }

        return $this;
    }

    /**
     * Adds event to observer.
     *
     * @param string $eventName
     * @param string $observerName
     * @param string $modelName
     * @param string $methodName
     */
    public function addObserverToEvent($eventName, $observerName, $modelName, $methodName)
    {
        $eventNode = new Varien_Simplexml_Element(
            "<config>
                <global>
                    <events>
                        <{$eventName}>
                            <observers>
                                <{$observerName}>
                                    <type>singleton</type>
                                    <class>{$modelName}</class>
                                    <method>{$methodName}</method>
                                 </{$observerName}>
                            </observers>
                        </{$eventName}>
                    </events>
                </global>
            </config>"
        );
        Mage::getConfig()->getNode()->extend($eventNode);
    }
}
