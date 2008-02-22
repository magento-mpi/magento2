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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product gallery attribute
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Ivan Chepurnyi <ivan.chepurnoy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery extends Varien_Data_Form_Element_Abstract
{

    public function getElementHtml()
    {
        $html = $this->getContentHtml();
        //$html.= $this->getAfterElementHtml();
        return $html;
    }


    /**
     * Prepares content block
     *
     * @return string
     */
    public function getContentHtml()
    {

        /* @var $content Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_Content */
        $content = Mage::getSingleton('core/layout')
            ->createBlock('adminhtml/catalog_product_helper_form_gallery_content');

        $content->setId($this->getHtmlId() . '_content')
            ->setElement($this);
        return $content->toHtml();
    }


    public function getLabel()
    {
        return '';
    }
} // Class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery End