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
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Core_Mage_LayeredNavigation_Helper extends Mage_Selenium_TestCase
{
    /**
     * Set category ID from link into UImap
     * @param string $categoryName
     */
    public function setCategoryIdFromLink($categoryName)
    {
        $this->addParameter('categoryName', $categoryName);
        $linkXpath = $this->_getControlXpath('link', 'category_name');
        $link = $this->getAttribute($linkXpath . '@href');
        // parse link received from xpath
        $parsedLink = parse_url($link);
        parse_str($parsedLink['query']);
        if(!isset($cat)){
            $this->fail('Impossible to define variable');
        }
        $this->addParameter('catid', $cat);
    }
}