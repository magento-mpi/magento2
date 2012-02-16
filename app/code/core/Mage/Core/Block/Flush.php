<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Immediate flush block. To be used only as root
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Block_Flush extends Mage_Core_Block_Abstract
{

    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }

        ob_implicit_flush();

        foreach ($this->getSortedChildren() as $name) {
            echo $this->getChildHtml($name);
        }
    }

}
