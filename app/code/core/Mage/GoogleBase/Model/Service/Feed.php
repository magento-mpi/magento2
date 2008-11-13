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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Base Feed Model
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Model_Service_Feed extends Mage_GoogleBase_Model_Service
{
    const ITEM_TYPES_LOCATION = 'http://www.google.com/base/feeds/itemtypes';
    const ITEMS_LOCATION = 'http://www.google.com/base/feeds/items';

    /**
     *  Google Base Feed Instance
     *
     *  @param    none
     *  @return	  Zend_Gdata_Feed
     */
    public function getFeed ($location = null)
    {
        $query = new Zend_Gdata_Query($location);
        $service = $this->getService();
        $feed = $service->getFeed($query);
        return $feed;
    }

    /**
     *  Description goes here...
     *
     *  @param    none
     *  @return	  void
     */
    public function getItemsStatsArray()
    {
        $feed = $this->getFeed(self::ITEMS_LOCATION);
        $result = array();
        foreach ($feed as $entry) {
            $draft = 'no';
            if (is_object($entry->getControl()) && is_object($entry->getControl()->getDraft())) {
                $draft = $entry->getControl()->getDraft()->getText();
            }
            $data = array(
                'draft'     => ($draft == 'yes' ? 1 : 0)
            );
//            $expiresArr = $entry->getGbaseAttribute('expiration_date');
//            if (isset($expiresArr[0]) && is_object($expiresArr[0])) {
//                $data['expires'] = Mage::getSingleton('googlebase/service_item')->gBaseDate2DateTime($expiresArr[0]->getText());
//            }
            $result[$entry->getId()->getText()] = $data;
        }
        return $result;
    }

    /**
     *  Returns Google Base recommended Item Types Collection
     *
     *  @param    none
     *  @return	  Varien_Data_Collection
     */
    public function getItemTypesCollection ()
    {
        if ($this->_itemTypesCollection instanceof Varien_Data_Collection) {
            return $this->_itemTypesCollection;
        }
        $location = self::ITEM_TYPES_LOCATION . '/' . Mage::app()->getLocale()->getLocale();
        $feed = $this->getFeed($location);

        $collection = new Varien_Data_Collection();
        $collection->setOrder('name', 'asc');
        foreach ($feed->entries as $entry) {
            $itemType = $entry->extensionElements[0]->text;
            $item = new Varien_Object();
            $item->setId($itemType);
            $item->setName($entry->title->text);
            $item->setLocation($entry->id->text);
            $collection->addItem($item);

            $attributesArr = $entry->extensionElements[1]->extensionElements;
            $attributesCollection = new Varien_Data_Collection();
            $item->setAttributesCollection($attributesCollection);
            if (is_array($attributesArr)) {
                foreach($attributesArr as $attr) {
                    $name = $attr->extensionAttributes['name']['value'];
                    $type = $attr->extensionAttributes['type']['value'];
                    $attribute = new Varien_Object();
                    $attribute->setId($name);
                    $attribute->setName($name);
                    $attribute->setType($type);
                    $attributesCollection->addItem($attribute);
                }
            }
        }
        $this->_itemTypesCollection = $collection;
        return $collection;
    }

    /**
     *  Returns Google Base Attributes Collection
     *
     *  @param    string $itemType Google Base Item Type
     *  @return	  Varien_Data_Collection
     */
    public function getAttributesCollection ($itemType)
    {
        $itemTypesCollection = $this->getItemTypesCollection();
        $collectionItem = $itemTypesCollection->getItemById($itemType);
        if ($collectionItem === null) {
            Mage::throwException('No such Item Type "%s" in Google Base to retrieve attributes', $itemType);
        }
        return $collectionItem->getAttributesCollection();
    }
}