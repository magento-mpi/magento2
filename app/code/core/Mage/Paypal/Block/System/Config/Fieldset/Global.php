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
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Fieldset renderer for PayPal global settings
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_System_Config_Fieldset_Global
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'paypal/system/config/fieldset/global.phtml';

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        foreach ($element->getSortedElements() as $field) {
            $this->setChild($field->getHtmlId(), $field);
        }
        return $this->toHtml();
    }

    /**
     * Return child checkbox html with hidden field for correct config values
     *
     * @param string $htmlId Element Html Id
     * @return string
     */
    public function getCheckboxHtml($htmlId)
    {
        $checkbox = $this->getChild($htmlId);
        if (!$checkbox) {
            return '';
        }
        $configValue = (string)$checkbox->getValue();
        if ($configValue) {
            $checkbox->setChecked(true);
        } else {
            $checkbox->setValue('1');
        }
        if ($checkbox->getCanUseDefaultValue() && $checkbox->getInherit()) {
            $checkbox->setDisabled(true);
        }

        $hidden = new Varien_Data_Form_Element_Hidden(array(
            'html_id' => $checkbox->getHtmlId() . '_value',
            'name' => $checkbox->getName(),
            'value' => '0'
        ));
        $hidden->setForm($checkbox->getForm());
        return $hidden->getElementHtml() . $checkbox->getElementHtml();
    }

    /**
     * Return "Use default" checkbox html
     *
     * @param string $checkboxId
     * @return string
     */
    public function getInheritCheckboxHtml($checkboxId)
    {
        $checkbox = $this->getChild($checkboxId);

        $inheritCheckbox = new Varien_Data_Form_Element_Checkbox(array(
            'html_id' => $checkboxId . '_inherit',
            'name' => preg_replace('/\[value\](\[\])?$/', '[inherit]', $checkbox->getName()),
            'value' => '1',
            'class' => 'checkbox config-inherit',
            'onclick' => 'toggleValueElements(this, $(\''.$checkboxId.'\').up())'
        ));
        if ($checkbox->getInherit()) {
            $inheritCheckbox->setChecked(true);
        }

        $inheritCheckbox->setForm($checkbox->getForm());
        return $inheritCheckbox->getElementHtml();
    }

    /**
     * Return label for "Use default" checkbox
     *
     * @param string $checkboxId
     * @return string
     */
    public function getInheritCheckboxLabelHtml($checkboxId)
    {
        $checkbox = $this->getChild($checkboxId);
        return sprintf('<label for="%s" class="inherit" title="%s">%s</label>',
            $checkboxId . '_inherit',
            $checkbox->getDefaultValue(),
            Mage::helper('adminhtml')->__('Use Default')
        );
    }

    /**
     * Check whether checkbox has "Use default" option or not
     *
     * @param string $checkboxId
     * @return string
     */
    public function canUseInherit($checkboxId)
    {
        $checkbox = $this->getChild($checkboxId);
        if ($checkbox && $checkbox->getCanUseDefaultValue()) {
            return true;
        }
        return false;
    }

    /**
     * Check whether checkbox has "Use default" option or not
     *
     * @param string $checkboxId
     * @return string
     */
    public function getItems()
    {
        $checkbox = $this->getChild($checkboxId);
        if ($checkbox && $checkbox->getCanUseDefaultValue()) {
            return true;
        }
        return false;
    }

    /**
     * Return URL for PayPal methods landing page or demo page
     *
     * @param string $method
     * @param string $type
     * @return string
     */
    public function getExternalUrl($method, $type)
    {
        $urls = array(
            'general' => array(
                'more' => 'https://merchant.paypal.com/cgi-bin/marketingweb?cmd=_render-content&content_ID=merchant/home'
            ),
            'paypal_express' => array(
                'demo' => 'https://merchant.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=merchant/demo_express_checkout',
                'more' => 'https://www.paypal.com/cgi-bin/webscr?cmd=_simple-referral-flow&partner_id=NB9WWHYEMVUMS&product_id=ECA'
            ),
            'paypal_standard' => array(
                'demo' => 'https://merchant.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=merchant/demo_WPS',
                'more' => 'https://www.paypal.com/cgi-bin/webscr?cmd=_simple-referral-flow&partner_id=NB9WWHYEMVUMS&product_id=WPSA'
            ),
            'paypal_direct' => array(
                'demo' => 'https://merchant.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=merchant/demo_wpp',
                'more' => 'https://www.paypal.com/cgi-bin/webscr?cmd=_simple-referral-flow&partner_id=NB9WWHYEMVUMS&product_id=WPPROA'
            ),
            'payflowpro' => array(
                'demo' => '',
                'more' => 'https://www.paypal.com/cgi-bin/webscr?cmd=_simple-referral-flow&partner_id=NB9WWHYEMVUMS&product_id=payflow_pro'
            ),
        );
        if (isset($urls[$method][$type])) {
            return $urls[$method][$type];
        }
        return '';
    }
}
