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
 * PayPal specific model for certificate based authentication
 */
namespace Magento\Paypal\Model;

class Cert extends \Magento\Core\Model\AbstractModel
{
    /**
     * Certificate base path
     */
    const BASEPATH_PAYPAL_CERT  = 'cert/paypal';

    /**
     * Initialize resource model
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
     * @return \Magento\Paypal\Model\Cert
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
     */
    public function getCertPath()
    {
        if (!$this->getContent()) {
            \Mage::throwException(__('The PayPal certificate does not exist.'));
        }

        $certFileName = sprintf('cert_%s_%s.pem', $this->getWebsiteId(), strtotime($this->getUpdatedAt()));
        $certFile = $this->_getBaseDir() . DS . $certFileName;

        if (!file_exists($certFile)) {
            $this->_createCertFile($certFile);
        }
        return $certFile;
    }

    /**
     * Create physical certificate file based on DB data
     *
     * @param string $file
     */
    protected function _createCertFile($file)
    {
        $certDir = $this->_getBaseDir();
        if (!is_dir($certDir)) {
            $ioAdapter = new \Magento\Io\File();
            $ioAdapter->checkAndCreateFolder($certDir);
        } else {
            $this->_removeOutdatedCertFile();
        }

        file_put_contents($file, \Mage::helper('Magento\Core\Helper\Data')->decrypt($this->getContent()));
    }

    /**
     * Check and remove outdated certificate file by website
     *
     * @return void
     */
    protected function _removeOutdatedCertFile()
    {
        $certDir = $this->_getBaseDir();
        if (is_dir($certDir)) {
            $entries = scandir($certDir);
            foreach ($entries as $entry) {
                if ($entry != '.' && $entry != '..' && strpos($entry, 'cert_' . $this->getWebsiteId()) !== false) {
                    unlink($certDir . DS . $entry);
                }
            }
        }
    }

    /**
     * Retrieve base directory for certificate
     *
     * @return string
     */
    protected function _getBaseDir()
    {
        return \Mage::getBaseDir('var') . DS . self::BASEPATH_PAYPAL_CERT;
    }

    /**
     * Delete assigned certificate file after delete object
     *
     * @return \Magento\Paypal\Model\Cert
     */
    protected function _afterDelete()
    {
        $this->_removeOutdatedCertFile();
        return $this;
    }
}
