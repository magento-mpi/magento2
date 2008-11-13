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
 * Google Base Item Model
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Model_Service_Item extends Mage_GoogleBase_Model_Service
{
    const DEFAULT_ITEM_TYPE = 'products';
    const DEFAULT_ATTRIBUTE_TYPE = 'text';

    /**
     * Object instance to populate entry data
     *
     * @var Varien_Object
     */
    protected $_object = null;

    /**
     * Item instance to update entry data
     *
     * @var Mage_GoogleBase_Model_Item
     */
    protected $_item = null;

    /**
     *  $_object Setter
     *
     *  @param    Varien_Object $object
     *  @return	  Mage_GoogleBase_Model_Service_Item
     */
    public function setObject($object)
    {
        $this->_object = $object;
        return $this;
    }

    /**
     *  $_object Getter
     *
     *  @return	  Varien_Object
     */
    public function getObject()
    {
        return $this->_object;
    }

    /**
     *  $_item Setter
     *
     *  @param    Mage_GoogleBase_Model_Item $item
     *  @return	  Mage_GoogleBase_Model_Service_Item
     */
    public function setItem($item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     *  $_item Getter
     *
     *  @return	  Mage_GoogleBase_Model_Item
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     *  Insert Item into Google Base
     *
     *  @return	  Zend_Gdata_Gbase_ItemEntry
     */
    public function insert()
    {
        $this->_checkItem();
        
        $service = $this->getService();
        $entry = $service->newItemEntry();
        $entry->setItemType( $this->_getItemType() );
        $this->setEntry($entry);
        $this->_prepareEnrtyForSave();
        $createdEntry = $service->insertGbaseItem($this->getEntry(), $this->getDryRun());
        
        $entryId = $createdEntry->getId();
        $published = $this->_gBaseDate2DateTime($createdEntry->getPublished()->getText());
        $this->getItem()
            ->setGbaseItemId($entryId)
            ->setPublished($published);

        $expirationsArr = $createdEntry->getGbaseAttribute('expiration_date');
        if (is_array($expirationsArr) && is_object($expirationsArr[0])) {
        	$expires = $this->_gBaseDate2DateTime($expirationsArr[0]->getText());
        	$this->getItem()->setExpires($expires);
        }

        return $createdEntry;
    }

    /**
     *  Update Item data in Google Base
     *
     *  @return	  Zend_Gdata_Gbase_ItemEntry
     */
    public function update()
    {
        $this->_checkItem();
        
        $service = $this->getService();
        $entry = $service->getGbaseItemEntry( $this->getItem()->getGbaseItemId() );
        $this->setEntry($entry);
        $this->_prepareEnrtyForSave();
        $updatedEntry = $service->updateGbaseItem($this->getEntry(), $this->getDryRun());
        return $updatedEntry;
    }

    /**
     *  Delete Item from Google Base
     *
     *  @return	  Zend_Gdata_Gbase_ItemFeed
     */
    public function delete()
    {
        $this->_checkItem();
        
        $service = $this->getService();
        $entry = $service->getGbaseItemEntry( $this->getItem()->getGbaseItemId() );
        return $service->deleteGbaseItem($entry, $this->getDryRun());
    }

    /**
     *  Hide Google Base Item
     *
     *  @param    none
     *  @return	  string
     */
    public function hide() 
    {
        $this->_saveDraft(true);
        return $this;
    }
    
    /**
     * 
     * access  public      
     * param   string $string 
     * return  string
     */
    public function activate() 
    {
        $this->_saveDraft(false);
        return $this;
    }
    
    /**
     * 
     * access  public      
     * param   string $string 
     * return  string
     */
    protected function _saveDraft ($yes = true) 
    {
        $this->_checkItem();
        
        $service = $this->getService();
        $entry = $service->getGbaseItemEntry( $this->getItem()->getGbaseItemId() );

        $draftText = $yes ? 'yes' : 'no';
        $draft = $service->newDraft($draftText);
        $control = $service->newControl($draft);
        
        $entry->setControl($control);
        $entry->save();
        return $this;
    }
    
    /**
     *  Prepare Entry data and attributes before saving in Google Base
     *
     *  @return	  Mage_GoogleBase_Model_Service_Item
     */
    protected function _prepareEnrtyForSave()
    {
        $object = $this->getObject();
        if (!($object instanceof Varien_Object)) {
            Mage::throwException('Object model is not specified to save Google Base entry');
        }

        $this->_setUniversalData();

        $entry = $this->getEntry();
        $attributes = $this->getAttributeValues();
        if (is_array($attributes) && count($attributes)) {
            foreach ($attributes as $name => $data) {

                $name = $this->_normalizeString($name);
                $value = isset($data['value']) ? $data['value'] : '';
                $type  = isset($data['type']) && $data['type'] ? $data['type'] : self::DEFAULT_ATTRIBUTE_TYPE;

                $gBaseItemAttribute = $entry->getGbaseAttribute($name);

                if ($value && isset($gBaseItemAttribute[0]) && is_object($gBaseItemAttribute[0])) {
                    $gBaseItemAttribute[0]->text = $value;
                }
                elseif ($value && $type) {
                    $entry->addGbaseAttribute($name, $value, $type);
                }
            }
        }
        return $this;
    }

    /**
     *  Assign values to universal attribute of entry
     *
     *  @param    none
     *  @return	  Mage_GoogleBase_Model_Service_Item
     */
    protected function _setUniversalData ()
    {
        $service = $this->getService();
        $object = $this->getObject();
        $entry = $this->getEntry();
    
        $title = $service->newTitle()->setText( $object->getName() );
        $entry->setTitle($title);
        
        if ($object->getUrl()) {
            $link = $service->newLink();
            $link->href = $object->getUrl();
            $link->title = $title->getText();
            $entry->setLink(array($link));
        }
        
        if ($object->getDescription()) {
            $content = $service->newContent()->setText( $object->getDescription() );
            $entry->setContent($content);
        }
        
        if ($object->getImageUrl()) {
            $entry->addGbaseAttribute('image_link', $object->getImageUrl(),'url');
        }
        
        if ($this->_getItemType() == 'products') {
        	$quantity = $object->getQty() ? $object->getQty() : 1;
        	$entry->addGbaseAttribute('quantity', $quantity, 'int');
        }

        return $this;
    }

    /**
     *  Return assign item type or default item type
     *
     *  @return	  string Google Base Item Type
     */
    protected function _getItemType()
    {
        return $this->getItemType() ? $this->getItemType() : self::DEFAULT_ITEM_TYPE;
    }

    /**
     *  Check Item Instance
     *
     *  @param    none
     *  @return	  void
     */
    protected function _checkItem() 
    {
        if (!($this->getItem() instanceof Mage_GoogleBase_Model_Item)) {
            Mage::throwException('Item model is not specified to delete Google Base entry');
        }
    }
    
    /**
     *  Prepare string
     *
     *  @param    string
     *  @return	  string
     */
    protected function _normalizeString($string)
    {
        $string = preg_replace('/([^a-z^0-9^_])+/','_',strtolower($string));
        $string = preg_replace('/_{2,}/','_',$string);
        return trim($string,'_');
    }
    
    /**
     * 
     * access  public      
     * param   string $string 
     * return  string
     */
    protected function _gBaseDate2DateTime ($gBaseDate) 
    {
    	return date('Y-m-d H:i:s', strtotime($gBaseDate));
    }
}