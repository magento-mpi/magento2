<?php
/**
 * Customer address model
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Mysql4_Address
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
    
    public function __construct() 
    {
        self::$_addressTable    = Mage::registry('resources')->getTableName('customer_resource', 'address');
        self::$_typeTable       = Mage::registry('resources')->getTableName('customer_resource', 'address_type');
        self::$_typeLinkTable   = Mage::registry('resources')->getTableName('customer_resource', 'address_type_link');
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
        
        $arr = self::$_read->fetchRow($select);
        return $arr;
    }
    
    /**
     * Load address types
     *
     * @param   $addressId
     * @return  array(
     *      [$typeId] => array(
     *          ['code']    => string,
     *          ['primary'] => bool
     *      )
     *  )
     */
    public function loadTypes($addressId)
    {
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
        
        $arr = array();
        $types = self::$_read->fetchAll($sql, array('address_id'=>$addressId));
        foreach ($types as $type) {
            $arr[$type['address_type_id']] = array(
                'code'      => $type['code'], 
                'primary'   => $type['is_primary']
            );
        }

        return $arr;
    }
    
    public function save(Mage_Customer_Model_Address $address, $useTransaction=true)
    {
        if ($useTransaction) {
            self::$_write->beginTransaction();
        }        
        
        $data = $this->_prepareSaveData($address);
        try {
            if ($address->getId()) {
                $condition = self::$_write->quoteInto('address_id=?', $address->getId());
                self::$_write->update(self::$_addressTable, $data, $condition);
            } else {
                self::$_write->insert(self::$_addressTable, $data);
                $address->setAddressId(self::$_write->lastInsertId());
            }
            $this->saveTypes($address);
            
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
    
    protected function _prepareSaveData(Mage_Customer_Model_Address $address)
    {
        $data = array(
            'address_id'    => $address->getId(), 
            'customer_id'   => $address->getCustomerId(), 
            'firstname'     => $address->getFirstname(), 
            'lastname'      => $address->getLastname(), 
            'postcode'      => $address->getPostcode(), 
            'street'        => $address->getStreet(-1), 
            'city'          => $address->getCity(), 
            'region'        => $address->getRegion(), 
            'region_id'     => $address->getRegionId(), 
            'country_id'    => $address->getCountryId(), 
            'company'       => $address->getCompany(), 
            'telephone'     => $address->getTelephone(), 
            'fax'           => $address->getFax()
        );
        
        if (empty($data['customer_id'])) {
            throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer', 'message')->error('CSTE021'));
        }
        
        $region = (int) $data['region'];
        if ($region && is_null($data['region_id'])) {
            $data['region_id'] = $region;
        }
        
        // Validate region id
        if (!empty($data['region_id'])) {
            $region = Mage::getModel('directory_resource', 'region')->load($data['region_id']);
            if ($region && $region->getCountryId() && $region->getCountryId() == $data['country_id']) {
                $data['region'] = $region->getName();
            }
            else {
                throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer', 'message')->error('CSTE022'));
            }
        }

        if ($types = $address->getData('types')) {
            $address->setTypes($types);
        }
        
        if ($primaryTypes = $address->getData('primary_types')) {
            $address->setPrimaryTypes($primaryTypes);
        }
        
        if (!empty($data['street'])) {
            $address->setStreet($data['street']);
            $data['street'] = $address->getStreet(-1);
        }
        
        return $data;
    }
    
    /**
     * Delete row from database table
     *
     * @param   Mage_Customer_Model_Address|int $rowId
     */
    public function delete($addressId)
    {
        self::$_write->beginTransaction();
        try {
            $condition = self::$_write->quoteInto('address_id=?', $addressId);
            $result = self::$_write->delete(self::$_addressTable, $condition);
            self::$_write->commit();
        }
        catch (Exception $e){
            self::$_write->rollBack();
            throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer', 'message')->error('CSTE023'));
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
        $langTable = Mage::registry('resources')->getTableName('customer_resource', 'address_type_language');
        
        $select = self::$_read->select()->from(self::$_typeTable)
            ->join($langTable, "$langTable.address_type_id=".self::$_typeTable.".address_type_id", "$langTable.name");
            
        $typesArr = self::$_read->fetchAll($select);
        $types = array();
        foreach ($typesArr as $type) {
            $types[$type[$by]] = $type;
        }

        return $types;
    }
    
    public function saveTypes(Mage_Customer_Model_Address $address)
    {
        // TODO: save types
        $primaryTypes = $address->getPrimaryTypes();
        if (is_array($primaryTypes)) {
            foreach ($primaryTypes as $typeId) {
                $this->_setAsPrimaryByType($address, $typeId);
            }
        }
    }
    
    protected function _setAsPrimaryByType(Mage_Customer_Model_Address $address, $typeId)
    {
        $sql = 'UPDATE '.self::$_typeLinkTable.' 
                SET is_primary=0 
                WHERE 
                    address_id IN(SELECT address_id FROM '.self::$_addressTable.' WHERE customer_id=:customer_id)
                    AND address_type_id=:type_id';
        self::$_write->query($sql, array('customer_id'=>$address->getCustomerId(), 'type_id'=>$typeId));
        //self::$_write->update(self::$_typeLinkTable, array('is_primary'=>0),self::$_write->quoteInto('address_type_id=?',$typeId));
        
        $condition = self::$_write->quoteInto('address_id=?',$address->getId()) . 
            ' AND ' .self::$_write->quoteInto('address_type_id=?',$typeId);
        self::$_write->delete(self::$_typeLinkTable, $condition);
        self::$_write->insert(self::$_typeLinkTable, array('address_id'=>$address->getId(), 'address_type_id'=>$typeId, 'is_primary'=>1));
    }
}