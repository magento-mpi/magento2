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
 * Application Submission block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Submission
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Constructor
     *
     * Setting grid_id, DOM destination element id
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('mobile_app_submit');
        $this->setDestElementId('content');
    }
}
