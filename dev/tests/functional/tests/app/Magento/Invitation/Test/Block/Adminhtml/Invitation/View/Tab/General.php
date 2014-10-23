<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Test\Block\Adminhtml\Invitation\View\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class General.
 * Tab for general invitation information.
 *
 */
class General extends Tab
{
    /**
     * Locator for invitation information rows.
     *
     * @var string
     */
    protected $invitationRows = '.invitation_information tr';

    /**
     * Locator for table row header cell.
     *
     * @var string
     */
    protected $invitationKey = 'tr th';

    /**
     * Locator for table row standard cell.
     *
     * @var string
     */
    protected $invitationValue = 'tr td';

    /**
     * Get Invitation information elements.
     *
     * @return Element[]
     */
    protected function getInvitationRows()
    {
        return $this->_rootElement->find($this->invitationRows)->getElements();
    }

    /**
     * Get Invitation data.
     *
     * @return array
     */
    public function getInvitationData()
    {
        $rows = $this->getInvitationRows();
        $data = [];
        foreach ($rows as $row) {
            $data[$row->find($this->invitationKey)->getText()] = $row->find($this->invitationValue)->getText();
        }

        return $data;
    }
}
