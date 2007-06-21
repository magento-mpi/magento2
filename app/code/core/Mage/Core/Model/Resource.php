<?php

/**
 * Resources and connections registry and factory
 *
 */
class Mage_Core_Model_Resource
{
    /**
     * Instances of classes for connection types
     *
     * @var array
     */
    protected $_connectionTypes = array();
    
    /**
     * Instances of actual connections
     *
     * @var array
     */
    protected $_connections = array();

    /**
     * Registry of resource entities
     *
     * @var array
     */
    protected $_entities = array();
 
    /**
     * Creates a connection to resource whenever needed
     *
     * @param string $name
     * @return mixed
     */
    public function getConnection($name)
    {
        if (!isset($this->_connections[$name])) {
            $conn = Mage::getConfig()->getResourceConnectionConfig($name);
            if ($conn && $conn->is('active', 1)) {
                $typeObj = $this->getConnectionTypeInstance((string)$conn->type);
                $this->_connections[$name] = $typeObj->getConnection($conn);
            }
            else {
                return false;
            }
        }
        return $this->_connections[$name];
    }
    
    /**
     * Get connection type instance
     *
     * Creates new if doesn't exist
     *
     * @param string $type
     * @return Mage_Core_Model_Resource_Type_Abstract
     */
    public function getConnectionTypeInstance($type)
    {
        if (!isset($this->_connectionTypes[$type])) {
            $config = Mage::getConfig()->getResourceTypeConfig($type);
            $typeClass = $config->getClassName();
            $this->_connectionTypes[$type] = new $typeClass();
        }
        return $this->_connectionTypes[$type];
    }

    /**
     * Get resource entity
     *
     * @param string $resource
     * @param string $entity
     * @return Varien_Simplexml_Config
     */
    public function getEntity($model, $entity)
    {
        return Mage::getConfig()->getNode("global/models/$model/entities/$entity");
    }
    
    /**
     * Get resource table name
     *
     * @param   string $model
     * @param   string $entity
     * @return  string
     */
    public function getTableName($modelEntity)
    {
        list($model, $entity) = explode('/', $modelEntity);
        $resourceModel = (string)Mage::getConfig()->getNode('global/models/'.$model.'/resourceModel');
        return (string)$this->getEntity($resourceModel, $entity)->table;
    }
    
    public function cleanDbRow(&$row) {
        if (!empty($row) && is_array($row)) {
            foreach ($row as $key=>&$value) {
                if (is_string($value) && $value==='0000-00-00 00:00:00') {
                    $value = '';
                }
            }
        }
        return $this;
    }
    
    
    public function createConnection($name, $type, $config)
    {
        if (!isset($this->_connections[$name])) {
            $typeObj = $this->getConnectionTypeInstance($type);
            $this->_connections[$name] = $typeObj->getConnection($config);
        }
        return $this->_connections[$name];
    }
    
    
    public function checkDbConnection()
    {
    	if (!$this->getConnection('core_read')) {
    		//Mage::registry('controller')->getFront()->getResponse()->setRedirect(Mage::getUrl('install'));
    	}
    }
}