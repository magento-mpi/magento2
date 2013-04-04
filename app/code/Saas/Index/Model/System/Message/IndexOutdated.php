<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Saas_Index_Model_System_Message_IndexOutdated extends  Mage_Index_Model_System_Message_IndexOutdated
{
    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        $helper = $this->_helperFactory->get('Saas_Index_Helper_Data');
        $url = $this->_urlBuilder->getUrl('adminhtml/process/list');
        $text = $helper->__('You need to refresh the search index. Please click <a href="%s">here</a>.', $url);
        return $text;
    }
}
