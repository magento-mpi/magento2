<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Column renderer for Invitee in invitations grid
 *
 */
class Magento_Invitation_Block_Adminhtml_Invitation_Grid_Column_Invitee
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Backend Url
     *
     * @var Magento_Backend_Model_Url
     */
    protected $_url;

    /**
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Backend_Model_Url $url
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Backend_Model_Url $url,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_url = $url;
    }

    /**
     * Render invitee email linked to its account edit page
     *
     * @param   Magento_Object $row
     * @return  string
     */
    protected function _getValue(Magento_Object $row)
    {
        if ($this->_authorization->isAllowed('Magento_Customer::manage')) {
            if (!$row->getReferralId()) {
                return '';
            }
            return '<a href="' . $this->_url->getUrl('*/customer/edit', array('id' => $row->getReferralId())) . '">'
                   . $this->escapeHtml($row->getData($this->getColumn()->getIndex())) . '</a>';
        } else {
            return parent::_getValue($row);
        }
    }
}
