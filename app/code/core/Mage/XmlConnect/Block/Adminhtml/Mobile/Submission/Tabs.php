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
 * Device submission tabs block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Submission_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Constructor
     * Setting view parameters, destination element DomId and title
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('mobile_app_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Manage Mobile App'));
    }
}
