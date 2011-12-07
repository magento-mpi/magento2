<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Xmlconnect Form field renderer interface
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_XmlConnect_Model_Simplexml_Form_Element_Renderer_Interface
{
    public function render(Mage_XmlConnect_Model_Simplexml_Form_Element_Abstract $element);
}
