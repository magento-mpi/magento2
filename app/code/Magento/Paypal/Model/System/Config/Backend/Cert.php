<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\System\Config\Backend;

use Magento\Framework\App\Filesystem;

/**
 * Backend model for saving certificate file in case of using certificate based authentication
 */
class Cert extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Paypal\Model\CertFactory
     */
    protected $_certFactory;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $_tmpDirectory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Paypal\Model\CertFactory $certFactory
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Paypal\Model\CertFactory $certFactory,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_certFactory = $certFactory;
        $this->_encryptor = $encryptor;
        $this->_tmpDirectory = $filesystem->getDirectoryRead(Filesystem::SYS_TMP);
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Process additional data before save config
     *
     * @return $this
     * @throws \Magento\Framework\Model\Exception
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
                throw new \Magento\Framework\Model\Exception(__('The PayPal certificate file is empty.'));
            }
            $this->setValue($_FILES['groups']['name'][$this->getGroupId()]['fields'][$this->getField()]['value']);
            $content = $this->_encryptor->encrypt($this->_tmpDirectory->readFile($tmpPath));
            $this->_certFactory->create()->loadByWebsite($this->getScopeId())->setContent($content)->save();
        }
        return $this;
    }

    /**
     * Process object after delete data
     *
     * @return $this
     */
    protected function _afterDelete()
    {
        $this->_certFactory->create()->loadByWebsite($this->getScopeId())->delete();
        return $this;
    }
}
