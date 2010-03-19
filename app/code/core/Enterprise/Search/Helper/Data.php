<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Search_Helper_Data extends Enterprise_Enterprise_Helper_Core_Abstract
{
    /**
     * Convert an object to an array
     *
     * @param object $object The object to convert
     * @return array
     */
    public function objectToArray($object)
    {
        if(!is_object($object) && !is_array($object)){
            return $object;
        }
        if(is_object($object)){
            $object = get_object_vars($object);
        }
        return array_map(array('Enterprise_Search_Helper_Data', 'objectToArray'), $object);
    }

    /**
     * Retrieve language code from store locale code
     *
     * @param int|null $storeId
     * @return string
     */
    public function getLanguageCode($storeId = null)
    {
        $store = Mage::app()->getStore($storeId);
        $localeCode = $store->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE);
        $codeParts = explode('_', $localeCode);
        if (isset($codeParts[0])) {
            return strtolower($codeParts[0]);
        }
        return null;
    }
}
