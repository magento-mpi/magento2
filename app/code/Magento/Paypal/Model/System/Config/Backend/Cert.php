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
     * @var \Magento\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $_tmpDirectory;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Paypal\Model\CertFactory $certFactory
     * @param \Magento\Encryption\EncryptorInterface $encryptor
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\Paypal\Model\CertFactory $certFactory,
        \Magento\Encryption\EncryptorInterface $encryptor,
        \Magento\App\Filesystem $filesystem,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_certFactory = $certFactory;
        $this->_encryptor = $encryptor;
        $this->_tmpDirectory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::SYS_TMP_DIR);
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
        $tmpPath = $this->_tmpDirectory->getRelativePath(
            $_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value']
        );
        if ($tmpPath && $this->_tmpDirectory->isExist($tmpPath)) {
            if (!$this->_tmpDirectory->stat($tmpPath)['size']) {
                throw new \Magento\Core\Exception(__('The PayPal certificate file is empty.'));
            }
            $this->setValue($_FILES['groups']['name'][$this->getGroupId()]['fields'][$this->getField()]['value']);
            $content = $this->_encryptor->encrypt($this->_tmpDirectory->readFile($tmpPath));
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
