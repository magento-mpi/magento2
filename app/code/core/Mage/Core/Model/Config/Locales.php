<?php
/**
 *  Locale configuration. Contains configuration from app/locale/[locale_Code]/*.xml files
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Locales extends Mage_Core_Model_Config_Base
{
    /**
     * @param Mage_Core_Model_Config_Loader_Locales $loader
     */
    public function __construct(Mage_Core_Model_Config_Loader_Locales $loader)
    {
        parent::__construct('<config />');
        $loader->load($this);
    }
}
