<?php
/**
 * Encrypted config field backend model
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Backend;

class Encrypted
    extends \Magento\Core\Model\Config\Value
    implements \Magento\App\Config\Data\ProcessorInterface
{
    /**
     * @var \Magento\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\Encryption\EncryptorInterface $encryptor,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_encryptor = $encryptor;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Magic method called during class serialization
     *
     * @return string[]
     */
    public function __sleep()
    {
        $properties = parent::__sleep();
        return array_diff($properties, array('_encryptor'));
    }

    /**
     * Magic method called during class un-serialization
     *
     * @return void
     */
    public function __wakeup()
    {
        parent::__wakeup();
        $this->_encryptor = \Magento\App\ObjectManager::getInstance()
            ->get('Magento\Encryption\EncryptorInterface');
    }

    /**
     * Decrypt value after loading
     *
     * @return void
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (!empty($value) && ($decrypted = $this->_encryptor->decrypt($value))) {
            $this->setValue($decrypted);
        }
    }

    /**
     * Encrypt value before saving
     *
     * @return void
     */
    protected function _beforeSave()
    {
        $value = (string)$this->getValue();
        // don't change value, if an obscured value came
        if (preg_match('/^\*+$/', $this->getValue())) {
            $value = $this->getOldValue();
        }
        if (!empty($value)) {
            $encrypted = $this->_encryptor->encrypt($value);
            if ($encrypted) {
                $this->setValue($encrypted);
            }
        }
    }

    /**
     * Process config value
     *
     * @param string $value
     * @return string
     */
    public function processValue($value)
    {
        return $this->_encryptor->decrypt($value);
    }
}
