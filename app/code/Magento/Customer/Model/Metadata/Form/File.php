<?php
/**
 * Form Element File Data Model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata\Form;

class File extends AbstractData
{
    /**
     * Validator for check not protected extensions
     *
     * @var \Magento\Core\Model\File\Validator\NotProtectedExtension
     */
    protected $_validatorNotProtectedExtensions;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * @var \Magento\Core\Model\File\Validator\NotProtectedExtension
     */
    protected $_fileValidator;

    /**
     * @var \Magento\App\Filesystem
     */
    protected $_fileSystem;

    /**
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Logger $logger
     * @param \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata $attribute
     * @param null $value
     * @param $entityTypeCode
     * @param bool $isAjax
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\File\Validator\NotProtectedExtension $fileValidator
     * @param \Magento\App\Filesystem $fileSystem
     */
    public function __construct(
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Logger $logger,
        \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata $attribute,
        $value = null,
        $entityTypeCode,
        $isAjax = false,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\File\Validator\NotProtectedExtension $fileValidator,
        \Magento\App\Filesystem $fileSystem
    ) {
        parent::__construct($locale, $logger, $attribute, $value, $entityTypeCode, $isAjax);
        $this->_coreData = $coreData;
        $this->_fileValidator = $fileValidator;
        $this->_fileSystem = $fileSystem;
    }

    /**
     * {@inheritdoc}
     */
    public function extractValue(\Magento\App\RequestInterface $request)
    {
        if ($this->getIsAjaxRequest()) {
            return false;
        }

        $extend = $this->_getRequestValue($request);

        $attrCode  = $this->getAttribute()->getAttributeCode();
        if ($this->_requestScope) {
            $value  = array();
            if (strpos($this->_requestScope, '/') !== false) {
                $scopes = explode('/', $this->_requestScope);
                $mainScope  = array_shift($scopes);
            } else {
                $mainScope  = $this->_requestScope;
                $scopes     = array();
            }

            if (!empty($_FILES[$mainScope])) {
                foreach ($_FILES[$mainScope] as $fileKey => $scopeData) {
                    foreach ($scopes as $scopeName) {
                        if (isset($scopeData[$scopeName])) {
                            $scopeData = $scopeData[$scopeName];
                        } else {
                            $scopeData[$scopeName] = array();
                        }
                    }

                    if (isset($scopeData[$attrCode])) {
                        $value[$fileKey] = $scopeData[$attrCode];
                    }
                }
            } else {
                $value = array();
            }
        } else {
            if (isset($_FILES[$attrCode])) {
                $value = $_FILES[$attrCode];
            } else {
                $value = array();
            }
        }

        if (!empty($extend['delete'])) {
            $value['delete'] = true;
        }

        return $value;
    }

    /**
     * Validate file by attribute validate rules
     * Return array of errors
     *
     * @param array $value
     * @return array
     */
    protected function _validateByRules($value)
    {
        $label  = $this->getAttribute()->getStoreLabel();
        $rules  = $this->getAttribute()->getValidationRules();
        $extension  = pathinfo($value['name'], PATHINFO_EXTENSION);

        if (!empty($rules['file_extensions'])) {
            $extensions = explode(',', $rules['file_extensions']);
            $extensions = array_map('trim', $extensions);
            if (!in_array($extension, $extensions)) {
                return array(
                    __('"%1" is not a valid file extension.', $label)
                );
            }
        }

        /**
         * Check protected file extension
         */
        if (!$this->_fileValidator->isValid($extension)) {
            return $this->_fileValidator->getMessages();
        }

        if (!is_uploaded_file($value['tmp_name'])) {
            return array(
                __('"%1" is not a valid file.', $label)
            );
        }

        if (!empty($rules['max_file_size'])) {
            $size = $value['size'];
            if ($rules['max_file_size'] < $size) {
                return array(
                    __('"%1" exceeds the allowed file size.', $label)
                );
            };
        }

        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function validateValue($value)
    {
        if ($this->getIsAjaxRequest()) {
            return true;
        }

        $errors     = array();
        $attribute  = $this->getAttribute();
        $label      = $attribute->getStoreLabel();

        $toDelete   = !empty($value['delete']) ? true : false;
        $toUpload   = !empty($value['tmp_name']) ? true : false;

        if (!$toUpload && !$toDelete && $this->_value) {
            return true;
        }

        if (!$attribute->isRequired() && !$toUpload) {
            return true;
        }

        if ($attribute->isRequired() && !$toUpload) {
            $errors[] = __('"%1" is a required value.', $label);
        }

        if ($toUpload) {
            $errors = array_merge($errors, $this->_validateByRules($value));
        }

        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function compactValue($value)
    {
        if ($this->getIsAjaxRequest()) {
            return $this;
        }

        $attribute = $this->getAttribute();
        $original  = $this->_value;
        $toDelete  = false;
        if ($original) {
            if (!$attribute->isRequired() && !empty($value['delete'])) {
                $toDelete  = true;
            }
            if (!empty($value['tmp_name'])) {
                $toDelete  = true;
            }
        }

        $path = $this->_fileSystem->getPath(\Magento\App\Filesystem::MEDIA_DIR) . '/' . $this->_entityTypeCode;

        $result = $original;
        // unlink entity file
        if ($toDelete) {
            $result = '';
            $file = $path . $original;
            $ioFile = new \Magento\Io\File();
            if ($ioFile->fileExists($file)) {
                $ioFile->rm($file);
            }
        }

        if (!empty($value['tmp_name'])) {
            try {
                $uploader = new \Magento\File\Uploader($value);
                $uploader->setFilesDispersion(true);
                $uploader->setFilenamesCaseSensitivity(false);
                $uploader->setAllowRenameFiles(true);
                $uploader->save($path, $value['name']);
                $fileName = $uploader->getUploadedFileName();
                $result = $fileName;
            } catch (\Exception $e) {
                $this->_logger->logException($e);
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function restoreValue($value)
    {
        return $this->_value;
    }

    /**
     * {@inheritdoc}
     */
    public function outputValue($format = \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_TEXT)
    {
        $output = '';
        if ($this->_value) {
            switch ($format) {
                case \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_JSON:
                    $output = array(
                        'value'     => $this->_value,
                        'url_key'   => $this->_coreData->urlEncode($this->_value)
                    );
                    break;
            }
        }

        return $output;
    }
}
