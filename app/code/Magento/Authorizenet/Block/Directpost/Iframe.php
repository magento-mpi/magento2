<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * DirectPost iframe block
 *
 * @category   Magento
 * @package    Magento_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Authorizenet_Block_Directpost_Iframe extends Magento_Core_Block_Template
{
    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $params = Mage::registry('authorizenet_directpost_form_params');
        if (is_null($params)) {
            $params = array();
        }
        $this->setParams($params);
        return parent::_prepareLayout();
    }
}
