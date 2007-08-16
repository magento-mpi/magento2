<?php
/**
 * Backend model for shipping table rates CSV importing
 *
 * @package     Mage
 * @subpackage  Eav
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Sergiy Lysak <sergey@varien.com>
 */

class Mage_Shipping_Model_Mysql4_Carrier_Tablerate_Backend_Import extends Mage_Core_Model_Mysql4_Abstract
// Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{

    /**
     *
     */
    protected $_resourceModel;

	/**
	 * DB connections list
	 *
	 * @var array
	 */
	protected $_connections = array();

	public function __construct()
	{

	}

    /**
     * Set connections for entity operations
     *
     * @param Zend_Db_Adapter_Abstract $read
     * @param Zend_Db_Adapter_Abstract $write
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function setConnection(Zend_Db_Adapter_Abstract $read, Zend_Db_Adapter_Abstract $write=null)
    {
        $this->_connections['read'] = $read;
        $this->_connections['write'] = $write ? $write : $read;
        return $this;
    }

    /**
     * Return DB connection
     *
     * @param	string		$type
     * @return	Zend_Db_Adapter_Abstract
     */
    public function getConnection($type)
    {
    	if (!isset($this->_connections[$type])) {
    		$this->_connections[$type] = Mage::getSingleton('core/resource')->getConnection('shipping_' . $type);
    	}
    	return $this->_connections[$type];
    }

    public function afterSave($object)
    {
        var_dump($object);
    	$storeId       = $object->getStoreId();
        $attributeId   = $this->getAttribute()->getId();
        $entityId	   = $object->getId();
        $entityIdField = $this->getEntityIdField();
        $entityTypeId  = $this->getAttribute()->getEntity()->getTypeId();

        $connection = $this->getConnection('write');

        $values = $object->getData($this->getAttribute()->getName());

        if(isset($values['position']))
        {
            foreach ((array)$values['position'] as $valueId => $position) {
                if ($valueId >= 0) {
    	            $condition = array(
    		            $connection->quoteInto('value_id = ?', $valueId)
    	            );
                    $data = array();
                    $data['position'] = $position;
    	            $connection->update($this->getTable(), $data, $condition);
                    $valueIds[$valueId] = $valueId;
                }
                else {
                    $data = array();
    		        $data[$entityIdField] 	= $entityId;
    		        $data['attribute_id'] 	= $attributeId;
    		        $data['store_id']	  	= $storeId;
    		        $data['position']		= $position;
    		        $data['entity_type_id'] = $entityTypeId;
    	            $connection->insert($this->getTable(), $data);
                    $valueIds[$valueId] = $connection->lastInsertId();
                }

                unset($uploadedFileName);

                $types = $this->getImageTypes();
                foreach ($types as  $type) {
                    try {
                        $uploader = new Varien_File_Uploader($this->getAttribute()->getName().'_'.$type.'['.$valueId.']');
                        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    }
                    catch (Exception $e){
                        continue;
                    }
//                    $uploader->save(Mage::getSingleton('core/store')->getConfig('system/filesystem/upload').'/'.$type.'/', 'image_'.$entityId.'_'.$valueIds[$valueId].'.'.'jpg');
                    if ($this->getAttribute()->getEntity()->getStoreId() == 0) {
                        $path = Mage::getSingleton('core/store')->getConfig('system/filesystem/upload');
                    }
                    else {
                        $path = $this->getAttribute()->getEntity()->getStore()->getConfig('system/filesystem/upload');
                    }
                    $uploader->save($path.'/'.$type.'/', 'image_'.$entityId.'_'.$valueIds[$valueId].'.'.'jpg');
    	            if (!isset($uploadedFileName)) {
                        $uploadedFileName = $uploader->getUploadedFileName();
                    }
    	        }

                if (isset($uploadedFileName)) {
    	            $condition = array(
    		            $connection->quoteInto('value_id = ?', $valueIds[$valueId])
    	            );
                    $data = array();
    		        $data['value']		  	= $uploadedFileName;
    	            $connection->update($this->getTable(), $data, $condition);
                }
                else {
                    if ($valueId<0) {
                        $values['delete'][] = $valueIds[$valueId];
                    }
                }
            }
        }

        if(isset($values['delete']))
        {
            foreach ((array)$values['delete'] as $valueId) {
                if ($valueId != '') {
    	            $condition = array(
    		            $connection->quoteInto('value_id = ?', $valueId)
    	            );
    	            $connection->delete($this->getTable(), $condition);
                }
    	    }
        }
    }

}
