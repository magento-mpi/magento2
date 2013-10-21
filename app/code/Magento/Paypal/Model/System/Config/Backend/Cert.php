<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for saving certificate file in case of using certificate based authentication
 */
namespace Magento\Paypal\Model\System\Config\Backend;

class Cert extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\Paypal\Model\CertFactory
     */
    protected $_certFactory;

    /**
     * @var \Magento\Encryption\EncryptionInterface
     */
    protected $_encryptor;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Paypal\Model\CertFactory $certFactory
     * @param \Magento\Encryption\EncryptionInterface $encryptor
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Config $config,
        \Magento\Paypal\Model\CertFactory $certFactory,
        \Magento\Encryption\EncryptionInterface $encryptor,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_certFactory = $certFactory;
        $this->_encryptor = $encryptor;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Process additional data before save config
     *
     * @return \Magento\Paypal\Model\System\Config\Backend\Cert
     * @throws \Magento\Core\Exception
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value) && !empty($value['delete'])) {
            $this->setValue('');
            $this->_certFactory->create()->loadByWebsite($this->getScopeId())->delete();
        }

        if (!isset($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'])) {
            return $this;
        }
        $tmpPath = $_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'];
        if ($tmpPath && file_exists($tmpPath)) {
            if (!filesize($tmpPath)) {
                throw new \Magento\Core\Exception(__('The PayPal certificate file is empty.'));
            }
            $this->setValue($_FILES['groups']['name'][$this->getGroupId()]['fields'][$this->getField()]['value']);
            $content = $this->_encryptor->encrypt(file_get_contents($tmpPath));
            $this->_certFactory->create()->loadByWebsite($this->getScopeId())
                ->setContent($content)
                ->save();
        }
        return $this;
    }

    /**
     * Process object after delete data
     *
     * @return \Magento\Paypal\Model\System\Config\Backend\Cert
     */
    protected function _afterDelete()
    {
        $this->_certFactory->create()->loadByWebsite($this->getScopeId())->delete();
        return $this;
    }
}
