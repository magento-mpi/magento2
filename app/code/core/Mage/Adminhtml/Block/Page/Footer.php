<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml footer block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Page_Footer extends Mage_Adminhtml_Block_Template
{
    const LOCALE_CACHE_LIFETIME = 7200;
    const LOCALE_CACHE_KEY      = 'footer_locale';
    const LOCALE_CACHE_TAG      = 'adminhtml';

    protected $_template = 'page/footer.phtml';

    protected function _construct()
    {
        $this->setShowProfiler(true);
    }

    public function getChangeLocaleUrl()
    {
        return $this->getUrl('adminhtml/index/changeLocale');
    }

    public function getUrlForReferer()
    {
        return $this->getUrlEncoded('*/*/*',array('_current'=>true));
    }

    public function getRefererParamName()
    {
        return Mage_Core_Controller_Varien_Action::PARAM_NAME_URL_ENCODED;
    }

    public function getLanguageSelect()
    {
        $locale  = Mage::app()->getLocale();
        $cacheId = self::LOCALE_CACHE_KEY . $locale->getLocaleCode();
        $html    = Mage::app()->loadCache($cacheId);

        if (!$html) {
            $html = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Html_Select')
                ->setName('locale')
                ->setId('interface_locale')
                ->setTitle(Mage::helper('Mage_Page_Helper_Data')->__('Interface Language'))
                ->setExtraParams('style="width:200px"')
                ->setValue($locale->getLocaleCode())
                ->setOptions($locale->getTranslatedOptionLocales())
                ->getHtml();
            Mage::app()->saveCache($html, $cacheId, array(self::LOCALE_CACHE_TAG), self::LOCALE_CACHE_LIFETIME);
        }

        return $html;
    }
}
