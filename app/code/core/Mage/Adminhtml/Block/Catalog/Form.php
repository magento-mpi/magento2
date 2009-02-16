<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Base block for rendering category and product forms
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Form extends Mage_Adminhtml_Block_Widget_Form
{
    const DEFAULT_RENDERER_ELEMENT          = 'adminhtml/widget_form_renderer_element';
    const DEFAULT_RENDERER_FIELDSET         = 'adminhtml/widget_form_renderer_fieldset';
    const DEFAULT_RENDERER_FIELDSET_ELEMENT = 'adminhtml/catalog_form_renderer_fieldset_element';

    const XML_PATH_DEFAULT_RENDERER_ELEMENT          = 'adminhtml/settings/widget_form_renderer_element';
    const XML_PATH_DEFAULT_RENDERER_FIELDSET         = 'adminhtml/settings/widget_form_renderer_fieldset';
    const XML_PATH_DEFAULT_RENDERER_FIELDSET_ELEMENT = 'adminhtml/settings/catalog_form_renderer_fieldset_element';

    protected function _prepareLayout()
    {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock(
                ($value = $this->_getConfigValue(self::XML_PATH_DEFAULT_RENDERER_ELEMENT)) ? $value : self::DEFAULT_RENDERER_FIELDSET)
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(
                ($value = $this->_getConfigValue(self::XML_PATH_DEFAULT_RENDERER_FIELDSET)) ? $value : self::DEFAULT_RENDERER_FIELDSET)
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                ($value = $this->_getConfigValue(self::XML_PATH_DEFAULT_RENDERER_FIELDSET_ELEMENT)) ? $value : self::DEFAULT_RENDERER_FIELDSET_ELEMENT)
        );
    }

    protected function _getConfigValue($path)
    {
        $value = (string) Mage::getConfig()->getNode($path);
        return strlen($value) > 0 ? $value : false;
    }
}