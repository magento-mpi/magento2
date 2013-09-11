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
namespace Magento\Enterprise\Model;

class Observer
{
    /**
     * Set hide survey question to session
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Enterprise\Model\Observer
     */
    public function setHideSurveyQuestion($observer)
    {
        \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->setHideSurveyQuestion(true);
        return $this;
    }
}
