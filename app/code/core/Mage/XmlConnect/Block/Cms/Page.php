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
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cms Page xml renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Cms_Page extends Mage_Cms_Block_Page
{
    /**
     * Render cms page output xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $pageXmlObj = new Mage_XmlConnect_Model_Simplexml_Element('<page></page>');
        $html = parent::_toHtml();
        return $html;
    }

    /**
     * Set Page Id
     *
     * @return Mage_XmlConnect_Block_Cms_Page
     */
    protected function _prepareLayout()
    {
        $identifier = $this->getRequest()->getParam('id');
        $this->setPageId($identifier);
        return parent::_prepareLayout();
    }
}
