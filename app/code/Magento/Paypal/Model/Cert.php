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

use Magento\Framework\Filesystem\Directory\WriteInterface;

/**
 * PayPal specific model for certificate based authentication
 */
class Cert extends \Magento\Framework\Model\AbstractModel
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
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->varDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::VAR_DIR);
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
     * @throws \Magento\Framework\Model\Exception
     */
    public function getCertPath()
    {
        if (!$this->getContent()) {
            throw new \Magento\Framework\Model\Exception(__('The PayPal certificate does not exist.'));
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
