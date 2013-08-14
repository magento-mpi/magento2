<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

$cookieRestrictionModel = Mage::getModel('Magento_Cms_Model_Page')
    ->load('privacy-policy-cookie-restriction-mode','identifier');

if ($cookieRestrictionModel->getId()) {
    $content = $cookieRestrictionModel->getContent();
    $replacment = '{{config path="general/store_information/street_line1"}} '
        . '{{config path="general/store_information/street_line2"}} '
        . '{{config path="general/store_information/city"}} '
        . '{{config path="general/store_information/postcode"}} '
        . '{{config path="general/store_information/region_id"}} '
        . '{{config path="general/store_information/country_id"}}';
    $content = preg_replace('/{{config path="general\\/store_information\\/address"}}/ims', $replacment, $content);
    $cookieRestrictionModel->setContent($content)->save();
}
