<?php
/**
 * {license_notice}
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Phoenix_Moneybookers_Block_Form extends Mage_Payment_Block_Form
{
    /**
     * Available locales for content URL generation
     *
     * @var array
     */
    protected $_supportedInfoLocales = array('de');

    /**
     * Default locale for content URL generation
     *
     * @var string
     */
    protected $_defaultInfoLocale = 'en';

    protected $_template = 'form.phtml';

    /**
     * Return payment logo image src
     *
     * @param string $payment Payment Code
     * @return string|bool
     */
    public function getPaymentImageSrc($payment)
    {
        if ($payment == 'moneybookers_obt') {
            $payment .= '_'.$this->getInfoLocale();
        }

        $design = Mage::getDesign();
        $images = array(
            'Phoenix_Moneybookers::images/' . $payment . '.png',
            'Phoenix_Moneybookers::images/' . $payment . '.gif'
        );
        foreach ($images as $image) {
            if (file_exists($design->getSkinFile($image))) {
                return $design->getSkinUrl($image);
            }
        }
        return false;
    }

    /**
     * Return supported locale for information text
     *
     * @return string
     */
    public function getInfoLocale()
    {
        $locale = substr(Mage::app()->getLocale()->getLocaleCode(), 0 ,2);
        if (!in_array($locale, $this->_supportedInfoLocales)) {
            $locale = $this->_defaultInfoLocale;
        }
        return $locale;
    }

    /**
     * Return info URL for eWallet payment
     *
     * @return string
     */
    public function getWltInfoUrl()
    {
        $locale = substr(Mage::app()->getLocale()->getLocaleCode(), 0 ,2);
        return 'http://www.moneybookers.com/app/?l=' . strtoupper($locale);
    }
}
