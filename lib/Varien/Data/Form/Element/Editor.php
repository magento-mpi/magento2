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
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form editor element
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Editor extends Varien_Data_Form_Element_Textarea
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);

        if($this->isEnabled()) {
            $this->setType('wysiwyg');
            $this->setExtType('wysiwyg');
        } else {
            $this->setType('textarea');
            $this->setExtType('textarea');
        }
    }

    public function getElementHtml()
    {
        if($this->isEnabled())
        {
            $config = $this->getConfig();
            $element = ($this->getState() == 'html') ? '' : $this->getHtmlId();
            $jsUrl = $this->getForm()->getParent()->getJsUrl();
            $jsSetupObject = 'wysiwyg' . $this->getHtmlId();

            $html = '
                <script type="text/javascript" src="'.$jsUrl.'tiny_mce/tiny_mce.js"></script>
                <script type="text/javascript" src="'.$jsUrl.'mage/adminhtml/wysiwyg/tiny_mce/setup.js"></script>

                <textarea name="'.$this->getName().'" title="'.$this->getTitle().'" id="'.$this->getHtmlId().'" class="textarea '.$this->getClass().'" '.$this->serialize($this->getHtmlAttributes()).' >'.$this->getEscapedValue().'</textarea>
                <a href="#" id="toggle'.$this->getHtmlId().'">'.($config->getToggleLinkTitle() ? $config->getToggleLinkTitle() : 'Add/Remove Editor').'</a>

                <script type="text/javascript">
                //<![CDATA[

                function imagebrowser(field_name, url, type, w) {
                    varienGlobalEvents.fireEvent("open_browser_callback", {win:w});
                }

				'.$jsSetupObject.' = new tinyMceWysiwygSetup("'.$this->getHtmlId().'", '.Zend_Json::encode($this->getConfig()).');

                '.($this->isHidden() ? '' : 'Event.observe(window, "load", '.$jsSetupObject.'.setup.bind('.$jsSetupObject.'));').'

				Event.observe("toggle'.$this->getHtmlId().'", "click", '.$jsSetupObject.'.toggle.bind('.$jsSetupObject.'));
                varienGlobalEvents.attachEventHandler("formSubmit", '.$jsSetupObject.'.onFormValidation.bind('.$jsSetupObject.'));
                varienGlobalEvents.attachEventHandler("tinymceBeforeSetContent", '.$jsSetupObject.'.beforeSetContent.bind('.$jsSetupObject.'));
                varienGlobalEvents.attachEventHandler("tinymceSaveContent", '.$jsSetupObject.'.saveContent.bind('.$jsSetupObject.'));
                varienGlobalEvents.attachEventHandler("open_browser_callback", '.$jsSetupObject.'.openImagesBrowser.bind('.$jsSetupObject.'));

				//]]>
                </script>';

            $html.= $this->getAfterElementHtml();
            return $html;
        }
        else
        {
            return parent::getElementHtml();
        }
    }

    public function getTheme()
    {
        if(!$this->hasData('theme')) {
            return 'simple';
        }

        return $this->getData('theme');
    }

    /**
     * Editor config retriever
     *
     * @param string $key Config var key
     * @return mixed
     */
    public function getConfig($key = null)
    {
        if ( !($this->getData('config') instanceof Varien_Object) ) {
            $config = new Varien_Object();
            $this->setConfig($config);
        }
        if ($key !== null) {
            return $this->getData('config')->getData($key);
        }
        return $this->getData('config');
    }

    /**
     * Check whether Wysiwyg is enabled or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        $enabledByConfig = in_array($this->getConfig('enabled'), array('enabled', 'hidden'));
        return $this->getWysiwyg() === true || $enabledByConfig;
    }

    /**
     * Check whether Wysiwyg is loaded on demand or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->getConfig('enabled') == 'hidden';
    }
}
