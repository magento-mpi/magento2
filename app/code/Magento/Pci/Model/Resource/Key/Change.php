<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pci\Model\Resource\Key;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Encryption key changer resource model
 * The operation must be done in one transaction
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Change extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Encryptor interface
     *
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * Filesystem directory write interface
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_directory;

    /**
     * System configuration structure
     *
     * @var \Magento\Backend\Model\Config\Structure
     */
    protected $_structure;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Backend\Model\Config\Structure $structure
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Model\Config\Structure $structure,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {
        $this->_encryptor = clone $encryptor;
        parent::__construct($resource);
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::CONFIG);
        $this->_structure = $structure;
    }

    /**
     * Initialize
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('core_config_data', 'config_id');
    }

    /**
     * Re-encrypt all encrypted data in the database
     *
     * TODO: seems not used
     *
     * @param bool $safe Specifies whether wrapping re-encryption into the database transaction or not
     * @return void
     * @throws \Exception
     */
    public function reEncryptDatabaseValues($safe = true)
    {
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
        } catch (\Exception $e) {
            if ($safe) {
                $this->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Change encryption key
     *
     * @param string|null $key
     * @return null|string
     * @throws \Exception
     */
    public function changeEncryptionKey($key = null)
    {

        // prepare new key, encryptor and new file contents
        $file = 'local.xml';

        if (!$this->_directory->isWritable($file)) {
            throw new \Exception(__('File %1 is not writeable.', $file));
        }

        $contents = $this->_directory->readFile($file);
        if (null === $key) {
            $key = md5(time());
        }
        $this->_encryptor->setNewKey($key);
        $contents = preg_replace(
            '/<key><\!\[CDATA\[(.+?)\]\]><\/key>/s',
            '<key><![CDATA[' . $this->_encryptor->exportKeys() . ']]></key>',
            $contents
        );

        // update database and local.xml
        $this->beginTransaction();
        try {
            $this->_reEncryptSystemConfigurationValues();
            $this->_reEncryptCreditCardNumbers();
            $this->_directory->writeFile($file, $contents);
            $this->commit();
            return $key;
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Gather all encrypted system config values and re-encrypt them
     *
     * @return void
     */
    protected function _reEncryptSystemConfigurationValues()
    {
        // look for encrypted node entries in all system.xml files
        /** @var \Magento\Backend\Model\Config\Structure $configStructure  */
        $configStructure = $this->_structure;
        $paths = $configStructure->getFieldPathsByAttribute(
            'backend_model',
            'Magento\Backend\Model\Config\Backend\Encrypted'
        );

        // walk through found data and re-encrypt it
        if ($paths) {
            $table = $this->getTable('core_config_data');
            $values = $this->_getReadAdapter()->fetchPairs(
                $this->_getReadAdapter()->select()->from(
                    $table,
                    array('config_id', 'value')
                )->where(
                    'path IN (?)',
                    $paths
                )->where(
                    'value NOT LIKE ?',
                    ''
                )
            );
            foreach ($values as $configId => $value) {
                $this->_getWriteAdapter()->update(
                    $table,
                    array('value' => $this->_encryptor->encrypt($this->_encryptor->decrypt($value))),
                    array('config_id = ?' => (int)$configId)
                );
            }
        }
    }

    /**
     * Gather saved credit card numbers from sales order payments and re-encrypt them
     *
     * @return void
     */
    protected function _reEncryptCreditCardNumbers()
    {
        $table = $this->getTable('sales_order_payment');
        $select = $this->_getWriteAdapter()->select()->from($table, array('entity_id', 'cc_number_enc'));

        $attributeValues = $this->_getWriteAdapter()->fetchPairs($select);
        // save new values
        foreach ($attributeValues as $valueId => $value) {
            $this->_getWriteAdapter()->update(
                $table,
                array('cc_number_enc' => $this->_encryptor->encrypt($this->_encryptor->decrypt($value))),
                array('entity_id = ?' => (int)$valueId)
            );
        }
    }
}
