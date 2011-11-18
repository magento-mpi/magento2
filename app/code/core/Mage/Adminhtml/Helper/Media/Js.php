<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Media library js helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Media_Js extends Mage_Core_Helper_Js
{

    public function __construct()
    {
         $this->_translateData = array(
            'Complete' => $this->__('Complete'),
            'File size should be more than 0 bytes' => $this->__('File size should be more than 0 bytes'),
            'Upload Security Error' => $this->__('Upload Security Error'),
            'Upload HTTP Error'     => $this->__('Upload HTTP Error'),
            'Upload I/O Error'     => $this->__('Upload I/O Error'),
            'SSL Error: Invalid or self-signed certificate'     => $this->__('SSL Error: Invalid or self-signed certificate'),
            'Tb' => $this->__('Tb'),
            'Gb' => $this->__('Gb'),
            'Mb' => $this->__('Mb'),
            'Kb' => $this->__('Kb'),
            'b' => $this->__('b')
         );
    }

    /**
     * Retrieve JS translator initialization javascript
     *
     * @return string
     */
    public function getTranslatorScript()
    {
        $script = 'if (typeof(Translator) == \'undefined\') {'
                . '    var Translator = new Translate('.$this->getTranslateJson().');'
                . '} else {'
                . '    Translator.add('.$this->getTranslateJson().');'
                . '}';
        return $this->getScript($script);
    }

}
