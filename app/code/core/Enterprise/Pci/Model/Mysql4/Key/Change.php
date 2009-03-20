<?php
class Enterprise_Pci_Model_Mysql4_Key_Change extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_encryptor;

    protected function _construct()
    {
        $this->_init('core/config_data', 'config_id');
    }

    public function reEncryptSystemConfigurationValues()
    {
        $configSections = Mage::getSingleton('adminhtml/config')->getSections();
        $paths = array();
        foreach ($configSections->xpath('//sections/*/groups/*/fields/*/backend_model') as $node) {
            if ('adminhtml/system_config_backend_encrypted' === (string)$node) {
                 $paths[] =
                    $node->getParent()->getParent()->getParent()->getParent()->getParent()->getName()
                    . '/' . $node->getParent()->getParent()->getParent()->getName()
                    . '/' . $node->getParent()->getName();
            }
        }
        if ($paths) {
            $table = $this->getTable('core/config_data');
            $values = $this->_getReadAdapter()->fetchPairs($this->_getReadAdapter()->select()
                ->from($table, array('config_id', 'value'))
                ->where('`path` IN (?)', $paths)
                ->where('`value` <> ?', ''));
            foreach ($values as $configId => $value) {
                $this->_getWriteAdapter()->update($table,
                    array('`value` = ?', $this->_encryptor->encrypt($this->_encryptor->decrypt($value))),
                    "config_id = {$configId}");
            }
        }
    }

    public function reEncryptCreditCardNumbers()
    {
        $valuesTable      = $this->getTable('sales/order_entity') . '_varchar';
        $attributesTable  = $this->getTable('eav/attribute');
        $entityTypesTable = $this->getTable('eav/entity_type');
        $attributeValues = $this->_getReadAdapter()->fetchPairs($this->_getReadAdapter()->select()
            ->from($valuesTable, array('value_id', 'value'))
            ->where('attribute_id = (' . $this->_getReadAdapter()->select()
                ->from(array('a' => $attributesTable), 'a.attribute_id')
                ->joinInner(array('t' => $entityTypesTable), 'a.entity_type_id = t.entity_type_id', array())
                ->where('a.attribute_code = ?', 'cc_number_enc')
                ->where('t.entity_type_code = ?', 'order_payment')
                ->limit(1)
             . ')')
        );
    }

    public function changeEncryptionKey($key)
    {
        $this->_encryptor = clone Mage::helper('core')->getEncryptor();
        $this->_encryptor->setNewKey($key);

        // 'order_payment'
        // 'cc_number_enc'
        // sales_order_entity_varchar

        $this->reEncryptCreditCardNumbers();

        exit;

        $this->beginTransaction();
        try {
            // $this->_getWriteAdapter()->
            // instantiate helper model with different key
            // re-encrypt everyting in database
            // write new key to local.xml
//            Mage::getSingleton('install/installer_config')->replaceTmpEncryptKey($key);
//            $this->commit();
$this->rollBack();
        }
        catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }
}
