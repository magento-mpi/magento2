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
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Widget to display link to CMS page
 *
 * @category   Mage
 * @package    Mage_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Cms_Block_Widget_Page_Link
    extends Mage_Core_Block_Html_Link
    implements Mage_Cms_Block_Widget_Interface
{
    /**
     * Initialize block
     */
    protected function _construct()
    {
        /*
         * Saving original data to make sure we
         * have it without any other data manipulations.
         */
        $this->setOrigData();
    }

    /**
     * Prepare page url. Use passed identifier
     * or retrieve such using passed page id.
     *
     * @return string
     */
    public function getHref()
    {
        $href = "";
        if ($this->getOrigData('href')) {
            $href = $this->getOrigData('href');
        } else if ($this->getOrigData('page_id')) {
            $href = Mage::getResourceSingleton('cms/page')->getCmsPageIdentifierById($this->getOrigData('page_id'));
            $this->setData('href', $href);
        }

        return Mage::app()->getStore()->getUrl('', array('_direct' => $href));
    }

    /**
     * Prepare anchor title attribute using passed title
     * as parameter or retrieve page title from DB using passed identifier or page id.
     *
     * @return string
     */
    public function getTitle()
    {
        $title = '';
        if ($this->getOrigData('title') !== null) {
            // compare to null used here bc user can specify blank title
            $title = $this->getOrigData('title');
        } else if ($this->getOrigData('href')) {
            $title = Mage::getResourceSingleton('cms/page')->getCmsPageTitleByIdentifier($this->getOrigData('href'));
        } else if ($this->getOrigData('page_id')) {
            $title = Mage::getResourceSingleton('cms/page')->getCmsPageTitleById($this->getOrigData('page_id'));
        }

        $this->setData('title', $title);

        return $title;
    }

    /**
     * Prepare anchor text using passed text as parameter.
     * If anchor text was not specified use title instead and
     * if title will be blank string, page identifier will be used.
     *
     * @return string
     */
    public function getAnchorText()
    {
        if ($this->getOrigData('anchor_text')) {
            return $this->getOrigData('anchor_text');
        }

        if ($this->getTitle()) {
            return $this->getTitle();
        }

        if (!$this->getOrigData('href') && $this->getOrigData('page_id')) {
            return Mage::getResourceSingleton('cms/page')->getCmsPageTitleById($this->getOrigData('page_id'));
        }

        return $this->getOrigData('href');
    }
}
