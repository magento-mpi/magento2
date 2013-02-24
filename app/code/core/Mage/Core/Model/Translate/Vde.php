<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Core_Model_Translate_Vde implements Mage_Core_Model_Translate_TranslateInterface
{
    /**
     * Indicator to hold state of whether inline translation is allowed within vde.
     *
     * @var bool
     */
    protected $_isAllowed;

    /**
     * Always default inline translation in vde to disabled.
     * Translation within the vde will be enabled by the client when the 'Edit' button is enabled.
     *
     * @param mixed $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        return false;
    }
}
