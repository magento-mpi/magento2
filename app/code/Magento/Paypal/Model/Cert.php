<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model;

/**
 * PayPal specific model for certificate based authentication
 */
use Magento\Filesystem\Directory\WriteInterface;

class Cert extends \Magento\Core\Model\AbstractModel
{
    /**
     * Certificate base path
     */
    const BASEPATH_PAYPAL_CERT = 'cert/paypal/';

    /**
     * @var WriteInterface
     */
    protected $varDirectory;

    /**
     * @var \Magento\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\App\Filesystem $filesystem,
        \Magento\Encryption\EncryptorInterface $encryptor,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->varDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);
        $this->encryptor = $encryptor;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Paypal\Model\Resource\Cert');
    }

    /**
     * Load model by website id
     *
     * @param int $websiteId
     * @param bool $strictLoad
     * @return $this
     */
    public function loadByWebsite($websiteId, $strictLoad = true)
    {
        $this->setWebsiteId($websiteId);
        $this->_getResource()->loadByWebsite($this, $strictLoad);
        return $this;
    }

    /**
     * Get path to PayPal certificate file, if file does not exist try to create it
     *
     * @return string
     * @throws \Magento\Core\Exception
     */
    public function getCertPath()
    {
        if (!$this->getContent()) {
            throw new \Magento\Core\Exception(__('The PayPal certificate does not exist.'));
        }

        $certFileName = sprintf('cert_%s_%s.pem', $this->getWebsiteId(), strtotime($this->getUpdatedAt()));
        $certFile = self::BASEPATH_PAYPAL_CERT . $certFileName;

        if (!$this->varDirectory->isExist($certFile)) {
            $this->_createCertFile($certFile);
        }
        return $this->varDirectory->getAbsolutePath($certFile);
    }

    /**
     * Create physical certificate file based on DB data
     *
     * @param string $file
     * @return void
     */
    protected function _createCertFile($file)
    {
        if ($this->varDirectory->isDirectory(self::BASEPATH_PAYPAL_CERT)) {
            $this->_removeOutdatedCertFile();
        }
        $this->varDirectory->writeFile($file, $this->encryptor->decrypt($this->getContent()));
    }

    /**
     * Check and remove outdated certificate file by website
     *
     * @return void
     */
    protected function _removeOutdatedCertFile()
    {
        $pattern = sprintf('cert_%s*' . $this->getWebsiteId());
        $entries = $this->varDirectory->search($pattern, self::BASEPATH_PAYPAL_CERT);
        foreach ($entries as $entry) {
            $this->varDirectory->delete($entry);
        }
    }

    /**
     * Delete assigned certificate file after delete object
     *
     * @return $this
     */
    protected function _afterDelete()
    {
        $this->_removeOutdatedCertFile();
        return $this;
    }
}
