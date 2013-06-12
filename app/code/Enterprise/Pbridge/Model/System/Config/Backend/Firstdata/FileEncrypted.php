<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

include_once 'easyarchive/EasyArchive.class.php';

/**
 * Firstdata Credentials Archive model
 *
 * @category   Enterprise
 * @package    Enterprise_Firstdata
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Model_System_Config_Backend_Firstdata_FileEncrypted extends Mage_Core_Model_Config_Data
{
    /**
     * Decrypt value after loading
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (!empty($value) && ($decrypted = Mage::helper('core')->decrypt($value))) {
            $this->setValue($decrypted);
        }
    }

    /**
     * Open uploaded archive and parse files for saving config values
     *
     * @return Enterprise_Pbridge_Model_System_Config_Backend_Firstdata_FileEncrypted
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value) && !empty($value['delete'])) {
            $this->setValue('');
            return $this;
        } else {
            $this->setValue($this->getOldValue());
        }

        if ($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value']){

            if (!$this->_checkExtension(
                $_FILES['groups']['name'][$this->getGroupId()]['fields'][$this->getField()]['value']
            )) {
                Mage::throwException(
                    Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Firstdata Certificate file uploaded wrong archive')
                );
            }

            $data = array();
            try {
                $tmpFile = $_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'];
                $tarGzFile = pathinfo($tmpFile, PATHINFO_FILENAME) . '.tar.gz';

                $varDir = Mage::getConfig()->getOptions()->getVarDir();
                $tarGzFile = $varDir . DS . $tarGzFile;

                if (!move_uploaded_file($tmpFile, $tarGzFile)) {
                    Mage::throwException(
                        Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Error during Firstdata Certificate file uploading')
                    );
                }

                $tarFile = $varDir . DS . pathinfo($tarGzFile, PATHINFO_FILENAME) . '.tar';
                $archive = new gzip();
                $archive->extractGzip($tarGzFile, $tarFile);

                $outputDir = $tarGzFile . 'dir';
                mkdir($outputDir);

                $archiveTar = new tar();
                $archiveTar->extractTar($tarFile, $outputDir . DS);

                $this->_getAuthInfo($outputDir, $data);
                $this->_getSslKey($outputDir, $data);
                $this->_getPrivateKey($outputDir, $data);
                $this->_getCertificate($outputDir, $data);

                $data = json_encode($data);

                $this->_cleanTmp($tarGzFile, $tarFile, $outputDir);
            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
            }

            if ($data && ($encrypted = Mage::helper('Mage_Core_Helper_Data')->encrypt($data))) {
                $this->setValue($encrypted);
            }
        }

        return $this;
    }

    /**
     * Check extension uploaded file
     *
     * @param string $fileName
     * @return bool
     */
    protected function _checkExtension($fileName)
    {
        return fnmatch('*.tar.gz', $fileName);
    }

    /**
     * Find file with authentication credentials and get username & password
     *
     * @param string $outputDir
     * @param array $data
     */
    protected function _getAuthInfo($outputDir, &$data)
    {
        $files = glob($outputDir . "/*.auth.txt");
        if ($files) {
            $match = array();
            preg_match(
                "/^.*Username: ([a-zA-Z0-9._]*) Password: ([a-zA-Z0-9]*)$/",
                file_get_contents($files[0], false),
                $match
            );
            if (count($match) == 3) {
                $data['username'] = $match[1];
                $data['password'] = $match[2];
            } else {
                Mage::throwException(
                    Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Firstdata Certificate file uploaded wrong archive')
                );
            }
        } else {
            Mage::throwException(
                Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Firstdata Certificate file uploaded wrong archive')
            );
        }
    }

    /**
     * Find file with ssl key and get it
     *
     * @param string $outputDir
     * @param array $data
     */
    protected function _getSslKey($outputDir, &$data)
    {
        $files = glob($outputDir . "/*.key.pw.txt");
        if ($files) {
            $data['ssl_key_passwd'] = file_get_contents($files[0], false);
        } else {
            Mage::throwException(
                Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Firstdata Certificate file uploaded wrong archive')
            );
        }
    }

    /**
     * Find file with private key and get it
     *
     * @param string $outputDir
     * @param array $data
     */
    protected function _getPrivateKey($outputDir, &$data)
    {
        $files = glob($outputDir . "/*.key");
        if ($files) {
            $data['private_key'] = file_get_contents($files[0], false);
        } else {
            Mage::throwException(
                Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Firstdata Certificate file uploaded wrong archive')
            );
        }
    }

    /**
     * Find file with certificate and get it
     *
     * @param string $outputDir
     * @param array $data
     */
    protected function _getCertificate($outputDir, &$data)
    {
        $files = glob($outputDir . "/*.pem");
        if ($files) {
            $data['certificate'] = file_get_contents($files[0], false);
        } else {
            Mage::throwException(
                Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Firstdata Certificate file uploaded wrong archive')
            );
        }
    }

    /**
     * Delete tmp files
     *
     * @param string $fileTarGz
     * @param string $fileTar
     * @param string $dir
     */
    protected function _cleanTmp($fileTarGz, $fileTar, $dir)
    {
        if (is_file($fileTarGz))  unlink($fileTarGz);
        if (is_file($fileTar))  unlink($fileTar);

        foreach (glob($dir . '/*') as $filename) {
            if ($filename != '.' && $filename != '..') {
                unlink($filename);
            }
        }
        rmdir($dir);
    }
}
