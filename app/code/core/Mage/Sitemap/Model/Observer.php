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
 * @package    Mage_Sitemap
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory module observer
 *
 * @author      Yuriy Scherbina <yuriy.scherbina@varien.com>
 */
class Mage_Sitemap_Model_Observer
{
    public function scheduledGenerateSitemaps($schedule)
    {
        $generateWarnings = array();

        $collection = Mage::getResourceModel('sitemap/sitemap_collection')
            ->load();

        foreach ($collection as $sitemap){
            if ($sitemap->getId()) {
                $xml = $sitemap->generateSitemap();
                $file = Mage::getBaseDir('base') . '/' . $sitemap->getSitemapPath() . '/' . $sitemap->getSitemapFilename();

                $file = str_replace('//', '/', $file);

                $fp = fopen($file, 'w');
                fputs($fp, $xml);
                fclose($fp);
            }
        }
    }
}