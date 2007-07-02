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
    protected $_read;
    protected $_write;
    protected $_addressTable;
    protected $_typeTable;
    protected $_typeLinkTable;

    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        $this->_read =  $resource->getConnection('customer_read');
        $this->_write = $resource->getConnection('customer_write');
        $this->_addressTable    = $resource->getTableName('customer/address');
        $this->_typeTable       = $resource->getTableName('customer/address_type');
        $this->_typeLinkTable   = $resource->getTableName('customer/address_type_link');
    }
    
    public function getIdFieldName()
    {
        return 'address_id';
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     * @return  Mage_Customer_Model_Address
     */
    public function load($addressId)
    {
        $select = $this->_getReadConnection()->select()
            ->from($this->_addressTable)
            ->where($this->_getReadConnection()->quoteInto('address_id=?', $addressId));
        
        $arr = $this->_getReadConnection()->fetchRow($select);
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
                    '.$this->_typeLinkTable.' AS at,
                    '.$this->_typeTable.' AS t
                WHERE
                    at.address_type_id=t.address_type_id
                    AND at.address_id=:address_id';
        
        $arr = array();
        $types = $this->_getReadConnection()->fetchAll($sql, array('address_id'=>$addressId));
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
            $this->_getWriteConnection()->beginTransaction();
        }        
        
        $data = $this->_prepareSaveData($address);
        try {
            if ($address->getId()) {
                $condition = $this->_getWriteConnection()->quoteInto('address_id=?', $address->getId());
                $this->_getWriteConnection()->update($this->_addressTable, $data, $condition);
            } else {
                $this->_getWriteConnection()->insert($this->_addressTable, $data);
                $address->setAddressId($this->_getWriteConnection()->lastInsertId());
            }
            $this->saveTypes($address);
            
            if ($useTransaction) {
                $this->_getWriteConnection()->commit();
            }
        }
        catch (Exeption $e) {
            if ($useTransaction) {
                $this->_getWriteConnection()->rollBack();
            }
            Mage::throwException('customer address save error');
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
            Mage::throwException('invalid customer id in address data');
            //throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer/message')->error('CSTE021'));
        }
        
        $region = (int) $data['region'];
        if ($region && is_null($data['region_id'])) {
            $data['region_id'] = $region;
        }
        
        // Validate region id
        if (!empty($data['region_id'])) {
            $region = Mage::getResourceModel('directory/region')->load($data['region_id']);
            if ($region && $region->getCountryId() && $region->getCountryId() == $data['country_id']) {
                $data['region'] = $region->getName();
            }
            else {
                Mage::throwException('invalid region for country specified in address');
                //throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer/message')->error('CSTE022'));
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
        $this->_getWriteConnection()->beginTransaction();
        try {
            $condition = $this->_getWriteConnection()->quoteInto('address_id=?', $addressId);
            $result = $this->_getWriteConnection()->delete($this->_addressTable, $condition);
            $this->_getWriteConnection()->commit();
        }
        catch (Exception $e){
            $this->_getWriteConnection()->rollBack();
            Mage::throwException('customer delete error');
            //throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer/message')->error('CSTE023'));
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
        $langTable = Mage::getSingleton('core/resource')->getTableName('customer/address_type_language');
        
        $select = $this->_getReadConnection()->select()->from($this->_typeTable)
            ->join($langTable, "$langTable.address_type_id=".$this->_typeTable.".address_type_id", "$langTable.name");
            
        $typesArr = $this->_getReadConnection()->fetchAll($select);
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
        $sql = 'UPDATE '.$this->_typeLinkTable.' 
                SET is_primary=0 
                WHERE 
                    address_id IN(SELECT address_id FROM '.$this->_addressTable.' WHERE customer_id=:customer_id)
                    AND address_type_id=:type_id';
        $this->_getWriteConnection()->query($sql, array('customer_id'=>$address->getCustomerId(), 'type_id'=>$typeId));
        //$this->_getWriteConnection()->update($this->_typeLinkTable, array('is_primary'=>0),$this->_getWriteConnection()->quoteInto('address_type_id=?',$typeId));
        
        $condition = $this->_getWriteConnection()->quoteInto('address_id=?',$address->getId()) . 
            ' AND ' .$this->_getWriteConnection()->quoteInto('address_type_id=?',$typeId);
        $this->_getWriteConnection()->delete($this->_typeLinkTable, $condition);
        $this->_getWriteConnection()->insert($this->_typeLinkTable, array('address_id'=>$address->getId(), 'address_type_id'=>$typeId, 'is_primary'=>1));
    }
}