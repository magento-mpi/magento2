<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Currency controller
 *
 * @category   Magento
 * @package    Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Controller;

class Currency extends \Magento\Core\Controller\Front\Action
{
    public function switchAction()
    {
        if ($curency = (string) $this->getRequest()->getParam('currency')) {
            \Mage::app()->getStore()->setCurrentCurrencyCode($curency);
        }
        $this->_redirectReferer(\Mage::getBaseUrl());
    }
}
