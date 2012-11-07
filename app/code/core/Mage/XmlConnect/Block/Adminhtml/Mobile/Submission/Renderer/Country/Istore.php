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
 * Country field renderer for iStore
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Submission_Renderer_Country_Istore
    extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'form/element/country/istore.phtml';
}
