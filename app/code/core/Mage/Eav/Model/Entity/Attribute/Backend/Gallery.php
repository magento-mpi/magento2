<?php
/**
 * Image gallery attribute backend
 *
 * @package     Mage
 * @subpackage  Eav
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Sergiy Lysak <sergey@varien.com>
 */

class Mage_Eav_Model_Entity_Attribute_Backend_Gallery extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
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

	protected $_imageTypes = array();

	protected $_images = null;

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
    		$this->_connections[$type] = Mage::getSingleton('core/resource')->getConnection('catalog_' . $type);
    	}

    	return $this->_connections[$type];
    }

	public function afterLoad($object)
    {
    	$storeId = $object->getStoreId();

        $attributeId   = $this->getAttribute()->getId();
        $entityId	   = $object->getId();
        $entityIdField = $this->getEntityIdField();

        // TOFIX
        $this->_images = new Mage_Eav_Model_Entity_Attribute_Backend_Gallery_Image_Collection($this->getConnection('read'));

        $this->_images->getSelectSql()
        	->from($this->getTable(), array('value_id', 'value', 'position'))
        	->where('store_id = ?', $storeId)
        	->where($entityIdField . ' = ?', $entityId)
        	->where('attribute_id = ?', $attributeId)
            ->order('position', 'asc');

        $object->setData($this->getAttribute()->getName(), $this->_images->load());
    }

    public function afterSave($object)
    {
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
                    $uploader->save(Mage::getSingleton('core/store')->getConfig('system/filesystem/upload').$type.'/', 'image_'.$entityId.'_'.$valueIds[$valueId].'.'.'jpg');
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
    	        $condition = array(
    		        $connection->quoteInto('value_id = ?', $valueId)
    	        );
    	        $connection->delete($this->getTable(), $condition);
    	    }
        }
    }

    public function getImageTypes()
    {
        return $this->_imageTypes;
    }

}
