<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Option\Type\File;

class ValidatorInfo extends Validator
{
    /**
     * @var \Magento\Core\Helper\File\Storage\Database
     */
    protected $coreFileStorageDatabase;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Framework\File\Size $fileSize
     * @param \Magento\Core\Helper\File\Storage\Database $coreFileStorageDatabase
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\Framework\File\Size $fileSize,
        \Magento\Core\Helper\File\Storage\Database $coreFileStorageDatabase
    ) {
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        parent::__construct($scopeConfig, $filesystem, $fileSize);
    }

    /**
     * @param array $optionValue
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return bool
     * @throws \Magento\Framework\Model\Exception
     */
    public function validate($optionValue, $option)
    {
        if (!is_array($optionValue)) {
            return false;
        }
        /**
         * @see \Magento\Catalog\Model\Product\Option\Type\File::_validateUploadFile()
         *              There setUserValue() sets correct fileFullPath only for
         *              quote_path. So we must form both full paths manually and
         *              check them.
         */
        $checkPaths = array();
        if (isset($optionValue['quote_path'])) {
            $checkPaths[] = $optionValue['quote_path'];
        }
        if (isset($optionValue['order_path']) && !$this->getUseQuotePath()) { // TODO: getUseQuotePath
            $checkPaths[] = $optionValue['order_path'];
        }
        $fileFullPath = null;
        $fileRelativePath = null;
        foreach ($checkPaths as $path) {
            if (!$this->rootDirectory->isFile($path)) {
                if (!$this->coreFileStorageDatabase->saveFileToFilesystem($fileFullPath)) {
                    continue;
                }
            }
            $fileFullPath = $this->rootDirectory->getAbsolutePath($path);
            $fileRelativePath = $path;
            break;
        }

        if ($fileFullPath === null) {
            return false;
        }

        try {
            $validatorChain = new \Zend_Validate();
            $validatorChain = $this->buildImageValidator($validatorChain, $option, $fileFullPath);
        } catch (NotImageException $notImage) {
            return false;
        }

        $result = false;
        if ($validatorChain->isValid($fileFullPath)) {
            $result = $this->rootDirectory->isReadable($fileRelativePath)
                && isset($optionValue['secret_key'])
                && $this->buildSecretKey($fileRelativePath) == $optionValue['secret_key'];

        } elseif ($validatorChain->getErrors()) {
            $errors = $this->getValidatorErrors($validatorChain->getErrors(), $optionValue, $option);

            if (count($errors) > 0) {
                throw new \Magento\Framework\Model\Exception(implode("\n", $errors));
            }
        } else {
            throw new \Magento\Framework\Model\Exception(__('Please specify the product\'s required option(s).'));
        }
        return $result;
    }

    /**
     * @param string $fileRelativePath
     * @return string
     */
    protected function buildSecretKey($fileRelativePath)
    {
        return substr(md5($this->rootDirectory->readFile($fileRelativePath)), 0, 20);
    }
}
