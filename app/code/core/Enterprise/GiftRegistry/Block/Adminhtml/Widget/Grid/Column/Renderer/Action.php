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
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Column renderer for gift registry items grid action column
 */
class Enterprise_GiftRegistry_Block_Adminhtml_Widget_Grid_Column_Renderer_Action
    extends Enterprise_Enterprise_Block_Adminhtml_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render gift registry item action as select html element
     *
     * @param  Varien_Object $row
     * @return string
     */
    protected function _getValue(Varien_Object $row)
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setId($this->getColumn()->getId())
            ->setName('items[' . $row->getItemId() . '][action]')
            ->setOptions($this->getColumn()->getOptions());
        return $select->getHtml();
    }
}
