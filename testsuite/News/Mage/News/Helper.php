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
 * @subpackage  tests
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
class News_Mage_News_Helper extends Mage_Selenium_TestCase
{
    /**
     * Create Admin User.
     *
     * @param Array $userData
     */
    public function createNews($newsData)
    {
        $userData = $this->arrayEmptyClear($newsData);
        $this->clickButton('add_new_news');
        $this->fillTab($newsData,'news_info',false);
        $first = (isset($newsData['news_title'])) ? $newsData['news_title'] : '';
        $last = (isset($newsData['author'])) ? $newsData['author'] : '';
        $param = $first . ' ' . $last;
        $this->addParameter('user_title_author', $param);
        if (array_key_exists('content', $userData)) {
            $this->openTab('content');
            $this->clickButton('htmleditor',false);
            $this->fillTab($newsData,'content',false);
        }
        $this->saveForm('save_news');
                       
    }

}