<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * String translation model
 *
 * @method Magento_Core_Model_Resource_Translate_String _getResource()
 * @method Magento_Core_Model_Resource_Translate_String getResource()
 * @method int getStoreId()
 * @method Magento_Core_Model_Translate_String setStoreId(int $value)
 * @method string getTranslate()
 * @method Magento_Core_Model_Translate_String setTranslate(string $value)
 * @method string getLocale()
 * @method Magento_Core_Model_Translate_String setLocale(string $value)
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Translate_String extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Magento_Core_Model_Resource_Translate_String');
    }

    public function setString($string)
    {
        $this->setData('string', $string);
        return $this;
    }

    /**
     * Retrieve string
     *
     * @return string
     */
    public function getString()
    {
        return $this->getData('string');
    }
}
