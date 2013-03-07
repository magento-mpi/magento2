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
 * Inline Translations PHP part
 */
class Mage_Core_Model_Translate_Inline extends Mage_Core_Model_Translate_InlineAbstract
{
    /**
     * Is enabled and allowed Inline Translates
     *
     * @param mixed $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        if (is_null($store)) {
            $store = Mage::app()->getStore();
        }
        if (!$store instanceof Mage_Core_Model_Store) {
            $store = Mage::app()->getStore($store);
        }

        if (is_null($this->_isAllowed)) {
            if (Mage::getDesign()->getArea() == 'adminhtml') {
                $active = Mage::getStoreConfigFlag('dev/translate_inline/active_admin', $store);
            } else {
                $active = Mage::getStoreConfigFlag('dev/translate_inline/active', $store);
            }
            $this->_isAllowed = $active && Mage::helper('Mage_Core_Helper_Data')->isDevAllowed($store);
        }

        /* @var $translate Mage_Core_Model_Translate */
        $translate = Mage::getSingleton('Mage_Core_Model_Translate');

        return $translate->getTranslateInline() && parent::isAllowed();
    }
}
