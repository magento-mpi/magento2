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
 * Core translate helper
 */
class Mage_Core_Helper_Translate extends Mage_Core_Helper_Abstract
{
    /**
     * Save translation data to database for specific area
     *
     * @param array $translate
     * @param string $area
     * @param string $returnType
     * @return string
     */
    public function apply($translate, $area, $returnType = 'json')
    {
        try {
            if ($area) {
                Mage::getDesign()->setArea($area);
            }

            $this->_translator->processAjaxPost($translate);
            $result = $returnType == 'json' ? "{success:true}" : true;
        } catch (Exception $e) {
            $result = $returnType == 'json' ? "{error:true,message:'" . $e->getMessage() . "'}" : false;
        }
        return $result;
    }

    /**
     * This method initializes the Translate object for this instance.
     * @param $localeCode string
     * @param $area string
     * @param $forceReload bool
     * @return \Mage_Core_Model_Translate
     */
    public function initTranslate($localeCode, $area, $forceReload)
    {
        /** @var $objectManager Mage_ObjectManager */
        $objectManager = Mage::getObjectManager();

        /** @var $config Mage_Core_Model_Translate_Config */
        $config = $objectManager->get('Mage_Core_Model_Translate_Config');
        $config->setArea($area);
        $config->setForceReload($forceReload);

        /** @var $translate Mage_Core_Model_Translate */
        $translate = $objectManager->get('Mage_Core_Model_Translate');
        $translate->setLocale($localeCode);

        $eventManager = $objectManager->get('Mage_Core_Model_Event_Manager');
        $eventManager->dispatch('translate_initialization_before', array(
            'translate_object' => $translate
        ));
        return $translate;
    }
}
