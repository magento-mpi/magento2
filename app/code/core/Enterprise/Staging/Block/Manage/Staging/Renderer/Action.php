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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Staging events history tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $href = $row->getData($this->getColumn()->getIndex());
        if ($this->getColumn()->getTitle()) {
            if ($this->getColumn()->getIndex() == $this->getColumn()->getTitle()) {
                $title = $href;
            } else {
                $title = $this->getColumn()->getTitle();
            }
        } else {
            $title = $this->__('click here');
        }

        if ($this->getColumn()->getLength() && strlen($title) > $this->getColumn()->getLength()) {
            $title = substr($title, 0, $this->getColumn()->getLength()) . '...';
        }

        return '<a href="'.$href.'" target="_blank">'.$title.'</a>';
    }
}
