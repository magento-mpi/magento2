<?php
/**
 * Customer address model
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Mysql4_Address extends Mage_Customer_Model_Address
{
    static protected $_addressTable;
    static protected $_typeTable;
    static protected $_typeLinkTable;

    /**
     * DB read object
     *
     * @var Zend_Db_Adapter_Abstract
     */
    static protected $_read;
    
    /**
     * DB write object
     *
     * @var Zend_Db_Adapter_Abstract
     */
    static protected $_write;
    
    public function __construct($data=array()) 
    {
        parent::__construct($data);
        
        self::$_addressTable    = Mage::registry('resources')->getTableName('customer', 'address');
        self::$_typeTable       = Mage::registry('resources')->getTableName('customer', 'address_type');
        self::$_typeLinkTable   = Mage::registry('resources')->getTableName('customer', 'address_type_link');
        self::$_read    = Mage::registry('resources')->getConnection('customer_read');
        self::$_write   = Mage::registry('resources')->getConnection('customer_write');
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     * @return  Mage_Customer_Model_Address
     */
    public function load($addressId)
    {
        $select = self::$_read->select()->from(self::$_addressTable)
            ->where(self::$_read->quoteInto('address_id=?', $addressId));
        
        $this->setData(self::$_read->fetchRow($select));
        
        $sql = 'SELECT
                    t.code,
                    t.address_type_id,
                    at.is_primary
                FROM
                    '.self::$_typeLinkTable.' AS at,
                    '.self::$_typeTable.' AS t
                WHERE
                    at.address_type_id=t.address_type_id
                    AND at.address_id=:address_id';
        $types = self::$_read->fetchAll($sql, array('address_id'=>$addressId));
        foreach ($types as $type) {
            $this->setType($type['address_type_id'], $type['code'], $type['is_primary']);
        }

        return $this;
    }
    
    public function save($useTransaction=true)
    {
        if ($useTransaction) {
            self::$_write->beginTransaction();
        }        
        
        $this->_prepareSaveData();
        try {
            if ($this->getAddressId()) {
                $condition = self::$_write->quoteInto('address_id=?', $this->getAddressId());
                self::$_write->update(self::$_addressTable, $this->getData(), $condition);
                $this->saveTypes();
            } else {
                self::$_write->insert(self::$_addressTable, $this->getData());
                $this->setAddressId(self::$_write->lastInsertId());
                $this->saveTypes();
            }
            
            if ($useTransaction) {
                self::$_write->commit();
            }
        }
        catch (Exeption $e) {
            if ($useTransaction) {
                self::$_write->rollBack();
            }
            throw $e;
        }
        
        return $this;
    }
    
    protected function _prepareSaveData()
    {
        $data= $this->__toArray(array('address_id', 'customer_id', 'firstname', 'lastname', 'postcode', 'street', 'city', 
            'region', 'region_id', 'country_id', 'company', 'telephone', 'fax'));
        
        if (empty($data['customer_id'])) {
            throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer_model', 'message')->error('CSTE021'));
        }

        // Validate region id
        if (!empty($data['region_id'])) {
            $region = Mage::getModel('directory', 'region')->load($data['region_id']);
            if ($region && $region->getCountryId() && $region->getCountryId() == $data['country_id']) {
                $data['region'] = $region->getName();
            }
            else {
                throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer_model', 'message')->error('CSTE022'));
            }
        }

        if ($types = $this->getData('types')) {
            $this->setTypes($types);
        }
        
        if ($primaryTypes = $this->getData('primary_types')) {
            $this->setPrimaryTypes($primaryTypes);
        }
        
        $this->setData($data);
        
        if (!empty($data['street'])) {
            $this->setStreet($data['street']);
        }
        
        return $this;
    }
    
    /**
     * Delete row from database table
     *
     * @param   Mage_Customer_Model_Address|int $rowId
     */
    public function delete()
    {
        self::$_write->beginTransaction();
        try {
            $condition = self::$_write->quoteInto('address_id=?', $this->getAddressId());
            $result = self::$_write->delete(self::$_addressTable, $condition);
            self::$_write->commit();
        }
        catch (Exception $e){
            self::$_write->rollBack();
            throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer_model', 'message')->error('CSTE023'));
        }
        return $this;
    }

    
    /**
     * Retrieve available address types with their name by language
     * 
     * Use specified field for key
     *
     * @param string $by code|id
     * @param string $langCode en
     * @return array
     */
    public function getAvailableTypes($by='code', $langCode='en')
    {
        $langTable = Mage::registry('resources')->getTableName('customer', 'address_type_language');
        
        $select = self::$_read->select()->from(self::$_typeTable)
            ->join($langTable, "$langTable.address_type_id=".self::$_typeTable.".address_type_id", "$langTable.name");
            
        $typesArr = self::$_read->fetchAll($select);
        $types = array();
        foreach ($typesArr as $type) {
            $types[$type[$by]] = $type;
        }

        return $types;
    }
    
    public function saveTypes()
    {
        // TODO: save types
        $primaryTypes = $this->getPrimaryTypes();
        if (is_array($primaryTypes)) {
            foreach ($primaryTypes as $typeId) {
                $this->_setAsPrimaryByType($typeId);
            }
        }
    }
    
    protected function _setAsPrimaryByType($typeId)
    {
        // TODO: set is_primary=0 for all custmer address by type
        //self::$_write->update(self::$_typeLinkTable, array('is_primary'=>0),self::$_write->quoteInto('address_type_id=?',$typeId));
        
        $condition = self::$_write->quoteInto('address_id=?',$this->getAddressId()) . 
            ' AND ' .self::$_write->quoteInto('address_type_id=?',$typeId);
        self::$_write->delete(self::$_typeLinkTable, $condition);
        self::$_write->insert(self::$_typeLinkTable, array('address_id'=>$this->getAddressId(), 'address_type_id'=>$typeId, 'is_primary'=>1));
    }
}