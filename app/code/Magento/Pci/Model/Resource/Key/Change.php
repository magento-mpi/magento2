<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pci\Model\Resource\Key;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\DeploymentConfig\EncryptConfig;

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
     * Configuration writer
     *
     * @var \Magento\Framework\App\DeploymentConfig\Writer
     */
    protected $_writer;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Backend\Model\Config\Structure $structure
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\App\DeploymentConfig\Writer $writer
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Model\Config\Structure $structure,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\App\DeploymentConfig\Writer $writer
    ) {
        $this->_encryptor = clone $encryptor;
        parent::__construct($resource);
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::CONFIG);
        $this->_structure = $structure;
        $this->_writer = $writer;
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
        // prepare new key, encryptor and new configuration segment
        if (!$this->_writer->checkIfWritable()) {
            throw new \Exception(__('Deployment configuration file is not writable.'));
        }

        if (null === $key) {
            $key = md5(time());
        }
        $this->_encryptor->setNewKey($key);

        $encryptSegment = new EncryptConfig([EncryptConfig::KEY_ENCRYPTION_KEY => $this->_encryptor->exportKeys()]);

        // update database and config.php
        $this->beginTransaction();
        try {
            $this->_reEncryptSystemConfigurationValues();
            $this->_reEncryptCreditCardNumbers();
            $this->_writer->update($encryptSegment);
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
        $table = $this->getTable('sales_flat_order_payment');
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
