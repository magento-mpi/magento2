<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ImportExport config model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Model_Config
{
    /**
     * Get data about models from specified config key.
     *
     * @static
     * @param string $configKey
     * @throws Magento_Core_Exception
     * @return array
     */
    public static function getModels($configKey)
    {
        $entities = array();

        foreach (Mage::getConfig()->getNode($configKey)->asCanonicalArray() as $entityType => $entityParams) {
            if (empty($entityParams['model_token'])) {
                Mage::throwException(
                    __('Please provide a correct model token tag.')
                );
            }
            $entities[$entityType] = array(
                'model' => $entityParams['model_token'],
                'label' => empty($entityParams['label']) ? $entityType : $entityParams['label']
            );
        }
        return $entities;
    }

    /**
     * Get model params as combo-box options.
     *
     * @static
     * @param string $configKey
     * @param boolean $withEmpty OPTIONAL Include 'Please Select' option or not
     * @return array
     */
    public static function getModelsComboOptions($configKey, $withEmpty = false)
    {
        $options = array();

        if ($withEmpty) {
            $options[] = array(
                'label' => __('-- Please Select --'),
                'value' => ''
            );
        }
        foreach (self::getModels($configKey) as $type => $params) {
            $options[] = array('value' => $type, 'label' => $params['label']);
        }
        return $options;
    }

    /**
     * Get model params as array of options.
     *
     * @static
     * @param string $configKey
     * @param boolean $withEmpty OPTIONAL Include 'Please Select' option or not
     * @return array
     */
    public static function getModelsArrayOptions($configKey, $withEmpty = false)
    {
        $options = array();
        if ($withEmpty) {
            $options[0] = __('-- Please Select --');
        }
        foreach (self::getModels($configKey) as $type => $params) {
            $options[$type] = $params['label'];
        }
        return $options;
    }
}
