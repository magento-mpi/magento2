<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Data converter
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Payment\Method;

class Converter
{
    /**
     * List of fields that has to be encrypted
     * Format: method_name => array(field1, field2, ... )
     *
     * @var array
     */
    protected $_encryptFields = array(
        'ccsave' => array(
            'cc_owner' => true,
            'cc_exp_year' => true,
            'cc_exp_month' => true,
        ),
    );

    /**
     * @var \Magento\Encryption\EncryptionInterface
     */
    protected $_encryptor;

    /**
     * @param \Magento\Encryption\EncryptionInterface $encryptor
     */
    public function __construct(
        \Magento\Encryption\EncryptionInterface $encryptor
    ) {
        $this->_encryptor = $encryptor;
    }

    /**
     * Check if specified field is encrypted
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @param string $filedName
     * @return bool
     */
    protected function _shouldBeEncrypted(\Magento\Core\Model\AbstractModel $object, $filedName)
    {
        $method = $object->getData('method');
        return isset($this->_encryptFields[$method][$filedName]) &&
            $this->_encryptFields[$method][$filedName];
    }

    /**
     * Decode data
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @param string $filedName
     * @return mixed
     */
    public function decode(\Magento\Core\Model\AbstractModel $object, $filedName)
    {
        $value = $object->getData($filedName);

        if ($this->_shouldBeEncrypted($object, $filedName)) {
            $value = $this->_encryptor->decrypt($value);
        }

        return $value;
    }

    /**
     * Encode data
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @param string $filedName
     * @return mixed
     */
    public function encode(\Magento\Core\Model\AbstractModel $object, $filedName)
    {
        $value = $object->getData($filedName);

        if ($this->_shouldBeEncrypted($object, $filedName)) {
            $value = $this->_encryptor->encrypt($value);
        }

        return $value;
    }
}
