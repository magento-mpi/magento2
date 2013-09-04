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
class Magento_Adminhtml_Block_Catalog_Helper_Form_Wysiwyg extends Magento_Data_Form_Element_Textarea
{
    /**
     * Adminhtml data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_backendData = null;

    /**
     * Catalog data
     *
     * @var Magento_Core_Model_ModuleManager
     */
    protected $_moduleManager = null;

    /**
     * @param Magento_Core_Model_ModuleManager $moduleManager
     * @param Magento_Backend_Helper_Data $backendData
     * @param array $attributes
     */
    public function __construct(
        Magento_Core_Model_ModuleManager $moduleManager,
        Magento_Backend_Helper_Data $backendData,
        array $attributes = array()
    ) {
        $this->_moduleManager = $moduleManager;
        $this->_backendData = $backendData;
        parent::__construct($attributes);
    }

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
            $html .= Mage::app()->getLayout()
                ->createBlock('Magento_Adminhtml_Block_Widget_Button', '', array('data' => array(
                    'label'   => __('WYSIWYG Editor'),
                    'type'    => 'button',
                    'disabled' => $disabled,
                    'class' => ($disabled) ? 'disabled action-wysiwyg' : 'action-wysiwyg',
                    'onclick' => 'catalogWysiwygEditor.open(\''
                        . $this->_backendData->getUrl('adminhtml/catalog_product/wysiwyg')
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
        if ($this->_moduleManager->isEnabled('Magento_Cms')) {
            return (bool)(Mage::getSingleton('Magento_Cms_Model_Wysiwyg_Config')->isEnabled()
                && $this->getEntityAttribute()->getIsWysiwygEnabled());
        }

        return false;
    }
}
