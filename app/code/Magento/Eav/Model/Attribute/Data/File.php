<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * EAV Entity Attribute File Data Model
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Attribute_Data_File extends Magento_Eav_Model_Attribute_Data_Abstract
{
    /**
     * Validator for check not protected extensions
     *
     * @var Magento_Core_Model_File_Validator_NotProtectedExtension
     */
    protected $_validatorNotProtectedExtensions;

    /**
     * Extract data from request and return value
     *
     * @param Zend_Controller_Request_Http $request
     * @return array|string
     */
    public function extractValue(Zend_Controller_Request_Http $request)
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
        $rules  = $this->getAttribute()->getValidateRules();
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
        /** @var $validator Magento_Core_Model_File_Validator_NotProtectedExtension */
        $validator = Mage::getSingleton('Magento_Core_Model_File_Validator_NotProtectedExtension');
        if (!$validator->isValid($extension)) {
            return $validator->getMessages();
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
     * Validate data
     *
     * @param array|string $value
     * @throws Magento_Core_Exception
     * @return boolean
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
     * @param Magento_Core_Model_Abstract $entity
     * @param array|string $value
     * @return Magento_Eav_Model_Attribute_Data_File
     */
    public function compactValue($value)
    {
        if ($this->getIsAjaxRequest()) {
            return $this;
        }

        $attribute = $this->getAttribute();
        $original  = $this->getEntity()->getData($attribute->getAttributeCode());
        $toDelete  = false;
        if ($original) {
            if (!$attribute->getIsRequired() && !empty($value['delete'])) {
                $toDelete  = true;
            }
            if (!empty($value['tmp_name'])) {
                $toDelete  = true;
            }
        }

        $path   = Mage::getBaseDir('media') . DS . $attribute->getEntity()->getEntityTypeCode();

        // unlink entity file
        if ($toDelete) {
            $this->getEntity()->setData($attribute->getAttributeCode(), '');
            $file = $path . $original;
            $ioFile = new Magento_Io_File();
            if ($ioFile->fileExists($file)) {
                $ioFile->rm($file);
            }
        }

        if (!empty($value['tmp_name'])) {
            try {
                $uploader = new Magento_File_Uploader($value);
                $uploader->setFilesDispersion(true);
                $uploader->setFilenamesCaseSensitivity(false);
                $uploader->setAllowRenameFiles(true);
                $uploader->save($path, $value['name']);
                $fileName = $uploader->getUploadedFileName();
                $this->getEntity()->setData($attribute->getAttributeCode(), $fileName);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return $this;
    }

    /**
     * Restore attribute value from SESSION to entity model
     *
     * @param array|string $value
     * @return Magento_Eav_Model_Attribute_Data_File
     */
    public function restoreValue($value)
    {
        return $this;
    }

    /**
     * Return formated attribute value from entity model
     *
     * @return string|array
     */
    public function outputValue($format = Magento_Eav_Model_Attribute_Data::OUTPUT_FORMAT_TEXT)
    {
        $output = '';
        $value  = $this->getEntity()->getData($this->getAttribute()->getAttributeCode());
        if ($value) {
            switch ($format) {
                case Magento_Eav_Model_Attribute_Data::OUTPUT_FORMAT_JSON:
                    $output = array(
                        'value'     => $value,
                        'url_key'   => Mage::helper('Magento_Core_Helper_Data')->urlEncode($value)
                    );
                    break;
            }
        }

        return $output;
    }
}
