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
            $jsSetupObject = 'wysiwyg' . $this->getHtmlId();

            $html = $this->_getLinksHtml()
                .'<textarea name="'.$this->getName().'" title="'.$this->getTitle().'" id="'.$this->getHtmlId().'"'.($this->getDisabled() ? ' disabled="disabled"' : '').' class="textarea '.$this->getClass().'" '.$this->serialize($this->getHtmlAttributes()).' >'.$this->getEscapedValue().'</textarea>

                <script type="text/javascript">
                //<![CDATA[

                function imagebrowser(fieldName, url, objectType, w) {
                    varienGlobalEvents.fireEvent("open_browser_callback", {win:w, type:objectType, field:fieldName});
                }

				'.$jsSetupObject.' = new tinyMceWysiwygSetup("'.$this->getHtmlId().'", '.Zend_Json::encode($this->getConfig()).');

                '.($this->isHidden() || $this->getDisabled() ? '' : 'Event.observe(window, "load", '.$jsSetupObject.'.setup.bind('.$jsSetupObject.'));').'

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
            if ($this->getConfig('widget_window_url')) {
                return $this->_getLinksHtml() . parent::getElementHtml();
            }
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
     * Return Editor top links HTML
     *
     * @return string
     */
    protected function _getLinksHtml()
    {
        $linksHtml = '<div id="links'.$this->getHtmlId().'"'.($this->getDisabled() ? ' style="display:none;"' : '').'>';
        if ($this->isEnabled()) {
            $linksHtml .= $this->_getToggleLinkHtml()
                . $this->_getLinksSeparatorHtml(false)
                . $this->_getPluginLinksHtml(false);
        } else {
            $linksHtml .= $this->_getPluginLinksHtml(true);
        }
        $linksHtml .= '</div>';

        return $linksHtml;
    }

    /**
     * Return HTML link to toggling WYSIWYG
     *
     * @return string
     */
    protected function _getToggleLinkHtml($visible = true)
    {
        if ($this->getDisabled()) {
            $visible = false;
        }
        return '<a href="#" id="toggle'.$this->getHtmlId().'"'.($visible ? '' : ' style="display:none;"').'>'.$this->translate('Show / Hide Editor').'</a>';
    }

    /**
     * Return HTML separator between links
     *
     * @param bool $visible
     * @return string
     */
    protected function _getLinksSeparatorHtml($visible = true)
    {
        return '<span class="'.$this->getHtmlId().'_sep"'.($visible ? '' : ' style="display:none;"').'> | </span>';
    }

    /**
     * Prepare Html links for additional WYSIWYG features
     *
     * @param bool $visible Display link or not
     * @return void
     */
    protected function _getPluginLinksHtml($visible = true)
    {
        $links = array();
        $linksHtml = array();

        // Link to media images insertion window
        $winUrl = $this->getConfig('files_browser_window_url');
        $links[] = new Varien_Data_Form_Element_Link(array(
            'href'      => '#',
            'title'     => $this->translate('Insert Image'),
            'value'     => $this->translate('Insert Image'),
            'onclick'   => "window.open('" . $winUrl . "', '" . $this->getHtmlId() . "', 'width=1024,height=800')",
            'class'     => $this->getHtmlId().'_link',
            'style'     => $visible ? '' : 'display:none',
        ));

        // Link to widget insertion window
        $winUrl = $this->getConfig('widget_window_no_wysiwyg_url');
        $links[] = new Varien_Data_Form_Element_Link(array(
            'href'      => '#',
            'title'     => $this->translate('Insert Widget'),
            'value'     => $this->translate('Insert Widget'),
            'onclick'   => "window.open('" . $winUrl . "', '" . $this->getHtmlId() . "', 'width=1024,height=800')",
            'class'     => $this->getHtmlId().'_link',
            'style'     => $visible ? '' : 'display:none',
        ));

        foreach ($links as $link) {
            $link->setForm($this->getForm());
            $linksHtml[] = $link->getElementHtml();
        }

        $linksHtml = implode($this->_getLinksSeparatorHtml($visible), $linksHtml);
        return $linksHtml;
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
     * Translate string using defined helper
     *
     * @param string $string String to be translated
     * @return string
     */
    public function translate($string)
    {
        if ($this->getConfig('translator') instanceof Varien_Object) {
            return $this->getConfig('translator')->__($string);
        }
        return $string;
    }

    /**
     * Check whether Wysiwyg is enabled or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        if ($this->hasData('wysiwyg')) {
            return $this->getWysiwyg();
        }
        return $this->getConfig('enabled');
    }

    /**
     * Check whether Wysiwyg is loaded on demand or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->getConfig('hidden');
    }
}
