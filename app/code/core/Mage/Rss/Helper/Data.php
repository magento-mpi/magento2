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
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Default rss helper
 *
 * @author      Lindy Kyaw <lindy@varien.com>
 */
class Mage_Rss_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_RSS_METHODS = 'rss';

    /**
     * Retrieve rss catalog feeds
     *
     * array structure:
     *
     * @return  array
     */
    public function getRssCatalogFeeds()
    {
        $section = Mage::getSingleton('adminhtml/config')->getSections();
        $catalogFeeds = $section->rss->groups->catalog->fields[0];
        $res = array();
        foreach($catalogFeeds as $code => $feed){
            $prefix = self::XML_PATH_RSS_METHODS.'/catalog/'.$code;
            if (!Mage::getStoreConfig($prefix) || $code=='tag') {
                continue;
            }
            $res[$code] = $feed;
        }
        return $res;
    }

    public function getCatalogRssUrl($code)
    {
        return Mage::getUrl('rss/catalog/'.$code);
    }
}