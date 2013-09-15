<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha image model
 *
 * @category   Magento
 * @package    Magento_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Captcha\Model\Config;

class Font
{
    /**
     * Captcha data
     *
     * @var Magento_Captcha_Helper_Data
     */
    protected $_captchaData = null;

    /**
     * @param Magento_Captcha_Helper_Data $captchaData
     */
    public function __construct(
        Magento_Captcha_Helper_Data $captchaData
    ) {
        $this->_captchaData = $captchaData;
    }

    /**
     * Get options for font selection field
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = array();
        foreach ($this->_captchaData->getFonts() as $fontName => $fontData) {
            $optionArray[] = array('label' => $fontData['label'], 'value' => $fontName);
        }
        return $optionArray;
    }
}
