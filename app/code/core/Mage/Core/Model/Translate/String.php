<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * String translation model
 *
 * @method Mage_Core_Model_Resource_Translate_String _getResource()
 * @method Mage_Core_Model_Resource_Translate_String getResource()
 * @method int getStoreId()
 * @method Mage_Core_Model_Translate_String setStoreId(int $value)
 * @method string getTranslate()
 * @method Mage_Core_Model_Translate_String setTranslate(string $value)
 * @method string getLocale()
 * @method Mage_Core_Model_Translate_String setLocale(string $value)
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Translate_String extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Translate_String');
    }
    
    public function setString($string)
    {
        $this->setData('string', $string);
        //$this->setData('string', strtolower($string));
        return $this;
    }
    
    /**
     * Retrieve string
     *
     * @return string
     */
    public function getString()
    {
        //return strtolower($this->getData('string'));
        return $this->getData('string');
    }
}
