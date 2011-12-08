<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cms Page xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cms_Page extends Mage_Cms_Block_Page
{
    /**
     * Page Id getter
     *
     * @return int
     */
    public function getPageId()
    {
        return $this->getRequest()->getParam('id');
    }
}
