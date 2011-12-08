<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Abstract container block with header
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Block_Template_Container extends Mage_Core_Block_Template
{

    /**
     * Set default template
     *
     */
    protected function _construct()
    {
        $this->setTemplate('template/container.phtml');
    }

}
