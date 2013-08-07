<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enterprise general observer
 *
 */
class Magento_Enterprise_Model_Observer
{
    /**
     * Set hide survey question to session
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Enterprise_Model_Observer
     */
    public function setHideSurveyQuestion($observer)
    {
        Mage::getSingleton('Magento_Backend_Model_Auth_Session')->setHideSurveyQuestion(true);
        return $this;
    }
}
