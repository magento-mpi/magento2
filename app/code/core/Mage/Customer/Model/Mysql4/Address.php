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
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer address model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
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
        $select = $this->_read->select()
            ->from($this->_addressTable)
            ->where('address_id=?', $addressId);
        
        $arr = $this->_read->fetchRow($select);
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
        $types = $this->_read->fetchAll($sql, array('address_id'=>$addressId));
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
            $this->_write->beginTransaction();
        }        
        
        $data = $this->_prepareSaveData($address);
        try {
            if ($address->getId()) {
                $condition = $this->_write->quoteInto('address_id=?', $address->getId());
                $this->_write->update($this->_addressTable, $data, $condition);
            } else {
                $this->_write->insert($this->_addressTable, $data);
                $address->setAddressId($this->_write->lastInsertId());
            }
            $this->saveTypes($address);
            
            if ($useTransaction) {
                $this->_write->commit();
            }
        }
        catch (Exeption $e) {
            if ($useTransaction) {
                $this->_write->rollBack();
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
        $this->_write->beginTransaction();
        try {
            $condition = $this->_write->quoteInto('address_id=?', $addressId);
            $result = $this->_write->delete($this->_addressTable, $condition);
            $this->_write->commit();
        }
        catch (Exception $e){
            $this->_write->rollBack();
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
        
        $select = $this->_read->select()->from($this->_typeTable)
            ->join($langTable, "$langTable.address_type_id=".$this->_typeTable.".address_type_id", "$langTable.name");
            
        $typesArr = $this->_read->fetchAll($select);
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
        $this->_write->query($sql, array('customer_id'=>$address->getCustomerId(), 'type_id'=>$typeId));
        //$this->_write->update($this->_typeLinkTable, array('is_primary'=>0),$this->_write->quoteInto('address_type_id=?',$typeId));
        
        $condition = $this->_write->quoteInto('address_id=?',$address->getId()) . 
            ' AND ' .$this->_write->quoteInto('address_type_id=?',$typeId);
        $this->_write->delete($this->_typeLinkTable, $condition);
        $this->_write->insert($this->_typeLinkTable, array('address_id'=>$address->getId(), 'address_type_id'=>$typeId, 'is_primary'=>1));
    }
}