<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Encryption key changer resource model
 * The operation must be done in one transaction
 *
 * @category    Enterprise
 * @package     Enterprise_Pci
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pci_Model_Resource_Key_Change extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @var Enterprise_Pci_Model_Encryption
     */
    protected $_encryptor;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_Resource $resource
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(Mage_Core_Model_Resource $resource, Magento_Filesystem $filesystem)
    {
        parent::__construct($resource);
        $this->_filesystem = $filesystem;
    }

    /**
     * Initialize
     *
     */
    protected function _construct()
    {
        $this->_init('core_config_data', 'config_id');
    }

    /**
     * Re-encrypt all encrypted data in the database
     *
     * @throws Exception
     * @param bool $safe Specifies whether wrapping re-encryption into the database transaction or not
     */
    public function reEncryptDatabaseValues($safe = true)
    {
        $this->_encryptor = clone Mage::helper('Mage_Core_Helper_Data')->getEncryptor();

        // update database only
        if ($safe) {
            $this->beginTransaction();
        }
        try {
            $this->_reEncryptSystemConfigurationValues();
            $this->_reEncryptCreditCardNumbers();
            if ($safe) {
                $this->commit();
            }
        }
        catch (Exception $e) {
            if ($safe) {
                $this->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Change encryption key
     *
     * @throws Exception
     * @param string $key
     * @return string
     */
    public function changeEncryptionKey($key = null)
    {
        $this->_filesystem->setWorkingDirectory(Mage::getBaseDir('etc'));
        // prepare new key, encryptor and new file contents
        $file = Mage::getBaseDir('etc') . DS . 'local.xml';

        if (!$this->_filesystem->isWritable($file)) {
            throw new Exception(Mage::helper('Enterprise_Pci_Helper_Data')
                ->__('File %s is not writeable.', $file));
        }

        $contents = $this->_filesystem->read($file);
        if (null === $key) {
            $key = md5(time());
        }
        $this->_encryptor = clone Mage::helper('Mage_Core_Helper_Data')->getEncryptor();
        $this->_encryptor->setNewKey($key);
        $contents = preg_replace('/<key><\!\[CDATA\[(.+?)\]\]><\/key>/s', 
            '<key><![CDATA[' . $this->_encryptor->exportKeys() . ']]></key>', $contents
        );

        // update database and local.xml
        $this->beginTransaction();
        try {
            $this->_reEncryptSystemConfigurationValues();
            $this->_reEncryptCreditCardNumbers();
            $this->_filesystem->write($file, $contents);
            $this->commit();
            return $key;
        }
        catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Gather all encrypted system config values and re-encrypt them
     *
     */
    protected function _reEncryptSystemConfigurationValues()
    {
        // look for encrypted node entries in all system.xml files
        /** @var Mage_Backend_Model_Config_Structure $configStructure  */
        $configStructure = Mage::getSingleton('Mage_Backend_Model_Config_Structure');
        $paths = $configStructure->getFieldPathsByAttribute(
            'backend_model',
            'Mage_Backend_Model_Config_Backend_Encrypted'
        );

        // walk through found data and re-encrypt it
        if ($paths) {
            $table = $this->getTable('core_config_data');
            $values = $this->_getReadAdapter()->fetchPairs($this->_getReadAdapter()->select()
                ->from($table, array('config_id', 'value'))
                ->where('path IN (?)', $paths)
                ->where('value NOT LIKE ?', '')
            );
            foreach ($values as $configId => $value) {
                $this->_getWriteAdapter()->update($table,
                    array('value' => $this->_encryptor->encrypt($this->_encryptor->decrypt($value))),
                    array('config_id = ?' => (int)$configId)
                );
            }
        }
    }

    /**
     * Gather saved credit card numbers from sales order payments and re-encrypt them
     *
     */
    protected function _reEncryptCreditCardNumbers()
    {
        $table = $this->getTable('sales_flat_order_payment');
        $select = $this->_getWriteAdapter()->select()
            ->from($table, array('entity_id', 'cc_number_enc'));

        $attributeValues = $this->_getWriteAdapter()->fetchPairs($select);
        // save new values
        foreach ($attributeValues as $valueId => $value) {
            $this->_getWriteAdapter()->update($table,
                array('cc_number_enc' => $this->_encryptor->encrypt($this->_encryptor->decrypt($value))),
                array('entity_id = ?' => (int)$valueId)
            );
        }
    }
}