<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product option file type
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Option_Type_File extends Mage_Catalog_Model_Product_Option_Type_Default
{
    /**
     * Validate user input for option
     *
     * @throws Mage_Core_Exception
     * @param array $values All product option values, i.e. array (option_id => mixed, option_id => mixed...)
     * @return Mage_Catalog_Model_Product_Option_Type_Default
     */
    public function validateUserValue($values)
    {
        $this->setIsValid(true);
        $option = $this->getOption();

        // Set option value from request (Admin/Front reorders)
        if (isset($values[$option->getId()]) && is_array($values[$option->getId()])) {
            $ok = isset($values[$option->getId()]['path']) && isset($values[$option->getId()]['size'])
                && is_file($values[$option->getId()]['path']) && is_readable($values[$option->getId()]['path'])
                && filesize($values[$option->getId()]['path']) == $values[$option->getId()]['size'];

            $this->setUserValue($ok ? $values[$option->getId()] : null);
            return $this;
        } elseif ($this->getProduct()->getSkipCheckRequiredOption()) {
            $this->setUserValue(null);
            return $this;
        }

        /**
         * Upload init
         */
        $upload = new Zend_File_Transfer_Adapter_Http();
        $file = 'options_' . $option->getId() . '_file';

        try {

            $runValidation = $option->getIsRequire() || $upload->isUploaded($file);
            if (!$runValidation) {
                $this->setUserValue(null);
                return $this;
            }

            $fileInfo = $upload->getFileInfo($file);
            $fileInfo = $fileInfo[$file];

        } catch (Exception $e) {
            $this->setIsValid(false);
            Mage::throwException(Mage::helper('catalog')->__("Files upload failed"));
        }

        $this->_createTargetDir();
        $upload->setDestination($this->getTargetDir());

        /**
         * Option Validations
         */

        // Image dimensions
        $_dimentions = array();
        if ($option->getImageSizeX() > 0) {
            $_dimentions['maxwidth'] = $option->getImageSizeX();
        }
        if ($option->getImageSizeY() > 0) {
            $_dimentions['maxheight'] = $option->getImageSizeY();
        }
        if (count($_dimentions) > 0) {
            $upload->addValidator('ImageSize', false, $_dimentions);
        }

        // File extension
        $_allowed = $this->_parseExtensionsString($option->getFileExtension());
        if ($_allowed !== null) {
            $upload->addValidator('Extension', false, $_allowed);
        } else {
            $_forbidden = $this->_parseExtensionsString($this->getConfigData('forbidden_extensions'));
            if ($_forbidden !== null) {
                $upload->addValidator('ExcludeExtension', false, $_forbidden);
            }
        }

        /**
         * Upload process
         */

        if ($upload->isUploaded($file) && $upload->isValid($file)) {
            $extension = pathinfo(strtolower($fileInfo['name']), PATHINFO_EXTENSION);
            $targetFileName = mt_rand(100, 999) . uniqid() . '.' . $extension;
            $upload->addFilter('Rename', array(
                'target' => $this->getTargetDir() . DS . $targetFileName,
                'overwrite' => false
            ));
            if (!$upload->receive()) {
                $this->setIsValid(false);
                Mage::throwException(Mage::helper('catalog')->__("File upload failed"));
            }

            $_imageSize = @getimagesize($this->getTargetDir() . DS . $targetFileName);
            if (is_array($_imageSize) && count($_imageSize) > 0) {
                $_width = $_imageSize[0];
                $_height = $_imageSize[1];
            } else {
                $_width = 0;
                $_height = 0;
            }

            $this->setUserValue(array(
                'type'          => $fileInfo['type'],
                'title'         => $fileInfo['name'],
                'name'          => $targetFileName,
                'path'          => $this->getTargetDir() . DS . $targetFileName,
                'size'          => $fileInfo['size'],
                'width'         => $_width,
                'height'        => $_height,
                'secret_key'    => mt_rand(1000000000, 100000000000)
            ));

        } elseif ($upload->getErrors()) {
            $errors = array();
            foreach ($upload->getErrors() as $errorCode) {
                switch ($errorCode) {
                    case Zend_Validate_File_ExcludeExtension::FALSE_EXTENSION:
                        $errors[] = Mage::helper('catalog')->__("The file '%s' has an invalid extension", $fileInfo['name']);
                        break;
                    case Zend_Validate_File_Extension::FALSE_EXTENSION:
                        $errors[] = Mage::helper('catalog')->__("The file '%s' has an invalid extension", $fileInfo['name']);
                        break;
                    case Zend_Validate_File_ImageSize::WIDTH_TOO_BIG:
                        $errors[] = Mage::helper('catalog')->__("Maximum allowed width for image '%s' should be '%s' px.",
                            $fileInfo['name'],
                            $option->getImageSizeX()
                        );
                        break;
                    case Zend_Validate_File_ImageSize::HEIGHT_TOO_BIG:
                        $errors[] = Mage::helper('catalog')->__("Maximum allowed height for image '%s' should be '%s' px.",
                            $fileInfo['name'],
                            $option->getImageSizeY()
                        );
                        break;
                    default:
                        break;
                }
            }
            if (count($errors) > 0) {
                $this->setIsValid(false);
                Mage::throwException( implode("\n", $errors) );
            }
        } else {
            $this->setIsValid(false);
            Mage::throwException(Mage::helper('catalog')->__('Please specify the product required option(s)'));
        }
        return $this;
    }

    /**
     * Prepare option value for cart
     *
     * @return mixed Prepared option value
     */
    public function prepareForCart()
    {
        if ($this->getIsValid() && $this->getUserValue() !== null) {
            $value = $this->getUserValue();
            // Save option in request, because we have no $_FILES['options']
            $requestOptions = $this->getRequest()->getOptions();
            $requestOptions[$this->getOption()->getId()] = $value;
            $this->getRequest()->setOptions($requestOptions);
            return serialize($value);
        } else {
            return null;
        }
    }

    /**
     * Return formatted option value for quote option
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getFormattedOptionValue($optionValue)
    {
        try {
            $value = unserialize($optionValue);
            if ($value !== false) {
                if ($value['width'] > 0 && $value['height'] > 0) {
                    $sizes = $value['width'] . ' x ' . $value['height'] . ' ' . Mage::helper('catalog')->__('px.');
                } else {
                    $sizes = '';
                }
                $result = sprintf('<a href="%s">%s</a> %s',
                    $this->_getOptionDownloadUrl($value['secret_key']),
                    Mage::helper('core')->htmlEscape($value['title']),
                    $sizes
                );
                return $result;
            }

            throw new Exception();

        } catch (Exception $e) {
            return $optionValue;
        }
    }

    /**
     * Return formatted option value ready to edit, ready to parse
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getEditableOptionValue($optionValue)
    {
        try {
            $value = unserialize($optionValue);
            if ($value !== false) {
                $result = sprintf('%s [%d]',
                    Mage::helper('core')->htmlEscape($value['title']),
                    $this->getQuoteItemOption()->getId()
                );
                return $result;
            }

            throw new Exception();

        } catch (Exception $e) {
            return $optionValue;
        }
    }

    /**
     * Parse user input value and return cart prepared value
     *
     * @param string $optionValue
     * @param array $productOptionValues Values for product option
     * @return string|null
     */
    public function parseOptionValue($optionValue, $productOptionValues)
    {
        // search quote item option Id in option value
        if (preg_match('/\[([0-9]+)\]/', $optionValue, $matches)) {
            $quoteItemOptionId = $matches[1];
            $option = Mage::getModel('sales/quote_item_option')->load($quoteItemOptionId);
            try {
                unserialize($option->getValue());
                return $option->getValue();
            } catch (Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Prepare option value for info buy request
     *
     * @param string $optionValue
     * @return mixed
     */
    public function prepareOptionValueForRequest($optionValue)
    {
        try {
            $result = unserialize($optionValue);
            return $result;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Destination directory full path
     *
     * @return string
     */
    public function getTargetDir()
    {
        return Mage::getBaseDir('media') . DS . 'custom_options';
    }

    /**
     * Return URL for option file download
     *
     * @return string
     */
    protected function _getOptionDownloadUrl($sekretKey)
    {
        return Mage::getUrl('catalog/product/downloadCustomOption', array(
            'id'  => $this->getQuoteItemOption()->getId(),
            'key' => $sekretKey
        ));
    }

    /**
     * Create destination directory for files uloading
     *
     * @throws Mage_Core_Exception
     * @return void
     */
    protected function _createTargetDir()
    {
        $io = new Varien_Io_File();
        if (!$io->isWriteable($this->getTargetDir()) && !$io->mkdir($this->getTargetDir())) {
            Mage::throwException(Mage::helper('catalog')->__("Cannot create writeable destination directory"));
        }
    }

    /**
     * Parse file extensions string with various separators
     *
     * @param string $extensions String to parse
     * @return array|null
     */
    protected function _parseExtensionsString($extensions)
    {
        preg_match_all('/[a-z]+/si', strtolower($extensions), $matches);
        if (isset($matches[0]) && is_array($matches[0]) && count($matches[0]) > 0) {
            return $matches[0];
        }
        return null;
    }
}