<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Attribute\Data;

use Magento\Framework\App\RequestInterface;

/**
 * EAV Entity Attribute File Data Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class File extends \Magento\Eav\Model\Attribute\Data\AbstractData
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
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $_directory;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\File\Validator\NotProtectedExtension $fileValidator
     * @param \Magento\Framework\App\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\File\Validator\NotProtectedExtension $fileValidator,
        \Magento\Framework\App\Filesystem $filesystem
    ) {
        parent::__construct($localeDate, $logger, $localeResolver);
        $this->_coreData = $coreData;
        $this->_fileValidator = $fileValidator;
        $this->_directory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::MEDIA_DIR);
    }

    /**
     * Extract data from request and return value
     *
     * @param RequestInterface $request
     * @return array|string|bool
     */
    public function extractValue(RequestInterface $request)
    {
        if ($this->getIsAjaxRequest()) {
            return false;
        }

        $extend = $this->_getRequestValue($request);

        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($this->_requestScope) {
            $value = array();
            if (strpos($this->_requestScope, '/') !== false) {
                $scopes = explode('/', $this->_requestScope);
                $mainScope = array_shift($scopes);
            } else {
                $mainScope = $this->_requestScope;
                $scopes = array();
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
     * @return string[]
     */
    protected function _validateByRules($value)
    {
        $label = $this->getAttribute()->getStoreLabel();
        $rules = $this->getAttribute()->getValidateRules();
        $extension = pathinfo($value['name'], PATHINFO_EXTENSION);

        if (!empty($rules['file_extensions'])) {
            $extensions = explode(',', $rules['file_extensions']);
            $extensions = array_map('trim', $extensions);
            if (!in_array($extension, $extensions)) {
                return array(__('"%1" is not a valid file extension.', $label));
            }
        }

        /**
         * Check protected file extension
         */
        if (!$this->_fileValidator->isValid($extension)) {
            return $this->_fileValidator->getMessages();
        }

        if (!is_uploaded_file($value['tmp_name'])) {
            return array(__('"%1" is not a valid file.', $label));
        }

        if (!empty($rules['max_file_size'])) {
            $size = $value['size'];
            if ($rules['max_file_size'] < $size) {
                return array(__('"%1" exceeds the allowed file size.', $label));
            }
        }

        return array();
    }

    /**
     * Validate data
     *
     * @param array|string $value
     * @return bool
     */
    public function validateValue($value)
    {
        if ($this->getIsAjaxRequest()) {
            return true;
        }

        $errors = array();
        $attribute = $this->getAttribute();
        $label = $attribute->getStoreLabel();

        $toDelete = !empty($value['delete']) ? true : false;
        $toUpload = !empty($value['tmp_name']) ? true : false;

        if (!$toUpload && !$toDelete && $this->getEntity()->getData($attribute->getAttributeCode())) {
            return true;
        }

        if (!$attribute->getIsRequired() && !$toUpload) {
            return true;
        }

        if ($attribute->getIsRequired() && !$toUpload) {
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
     * Export attribute value to entity model
     *
     * @param array|string $value
     * @return $this
     */
    public function compactValue($value)
    {
        if ($this->getIsAjaxRequest()) {
            return $this;
        }

        $attribute = $this->getAttribute();
        $original = $this->getEntity()->getData($attribute->getAttributeCode());
        $toDelete = false;
        if ($original) {
            if (!$attribute->getIsRequired() && !empty($value['delete'])) {
                $toDelete = true;
            }
            if (!empty($value['tmp_name'])) {
                $toDelete = true;
            }
        }

        $destinationFolder = $attribute->getEntity()->getEntityTypeCode();

        // unlink entity file
        if ($toDelete) {
            $this->getEntity()->setData($attribute->getAttributeCode(), '');
            $file = $destinationFolder . $original;
            if ($this->_directory->isExist($file)) {
                $this->_directory->delete($file);
            }
        }

        if (!empty($value['tmp_name'])) {
            try {
                $uploader = new \Magento\Framework\File\Uploader($value);
                $uploader->setFilesDispersion(true);
                $uploader->setFilenamesCaseSensitivity(false);
                $uploader->setAllowRenameFiles(true);
                $uploader->save($this->_directory->getAbsolutePath($destinationFolder), $value['name']);
                $fileName = $uploader->getUploadedFileName();
                $this->getEntity()->setData($attribute->getAttributeCode(), $fileName);
            } catch (\Exception $e) {
                $this->_logger->logException($e);
            }
        }

        return $this;
    }

    /**
     * Restore attribute value from SESSION to entity model
     *
     * @param array|string $value
     * @return $this
     */
    public function restoreValue($value)
    {
        return $this;
    }

    /**
     * Return formated attribute value from entity model
     *
     * @param string $format
     * @return string|array
     */
    public function outputValue($format = \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_TEXT)
    {
        $output = '';
        $value = $this->getEntity()->getData($this->getAttribute()->getAttributeCode());
        if ($value) {
            switch ($format) {
                case \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_JSON:
                    $output = array('value' => $value, 'url_key' => $this->_coreData->urlEncode($value));
                    break;
            }
        }

        return $output;
    }
}
