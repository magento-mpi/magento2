<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source for allowed locales
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Source_AllowedLocales
{
    /**
     * Cache of option array
     *
     * @var array
     */
    private $_options;

    /**
     * Return arra of options (array(array(value =>, label =>), ...))
     *
     * @throws RuntimeException
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array();
            $allowedLocales = Mage::getSingleton('Saas_PrintedTemplate_Model_Config')
                ->getConfigSectionArray('allowed_locales');

            foreach ($allowedLocales as $code => $description) {
                $this->_options[] = array('value' => $code, 'label' => $description);
            }
        }

        return $this->_options;
    }
}