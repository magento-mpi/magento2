<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog textarea attribute WYSIWYG button
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Helper\Form;

class Wysiwyg extends \Magento\Data\Form\Element\Textarea
{
    /**
     * Retrieve additional html and put it at the end of element html
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        if ($this->getIsWysiwygEnabled()) {
            $disabled = ($this->getDisabled() || $this->getReadonly());
            $html .= \Mage::app()->getLayout()
                ->createBlock('Magento\Adminhtml\Block\Widget\Button', '', array('data' => array(
                    'label'   => __('WYSIWYG Editor'),
                    'type'    => 'button',
                    'disabled' => $disabled,
                    'class' => ($disabled) ? 'disabled action-wysiwyg' : 'action-wysiwyg',
                    'onclick' => 'catalogWysiwygEditor.open(\''
                        . \Mage::helper('Magento\Adminhtml\Helper\Data')->getUrl('adminhtml/catalog_product/wysiwyg')
                        . '\', \'' . $this->getHtmlId().'\')'
                )))->toHtml();
            $html .= <<<HTML
<script type="text/javascript">
jQuery('#{$this->getHtmlId()}')
    .addClass('wysiwyg-editor')
    .data(
        'wysiwygEditor',
        new tinyMceWysiwygSetup(
            '{$this->getHtmlId()}',
             {
                settings: {
                    theme_advanced_buttons1 : 'bold,italic,|,justifyleft,justifycenter,justifyright,|,' +
                        'fontselect,fontsizeselect,|,forecolor,backcolor,|,link,unlink,image,|,bullist,numlist,|,code',
                    theme_advanced_buttons2: null,
                    theme_advanced_buttons3: null,
                    theme_advanced_buttons4: null,
                    theme_advanced_statusbar_location: null
                }
            }
        ).turnOn()
    );
</script>
HTML;
        }
        return $html;
    }

    /**
     * Check whether wysiwyg enabled or not
     *
     * @return boolean
     */
    public function getIsWysiwygEnabled()
    {
        if (\Mage::helper('Magento\Catalog\Helper\Data')->isModuleEnabled('Magento_Cms')) {
            return (bool)(\Mage::getSingleton('Magento\Cms\Model\Wysiwyg\Config')->isEnabled()
                && $this->getEntityAttribute()->getIsWysiwygEnabled());
        }

        return false;
    }
}
