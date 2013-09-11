<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mass-action block for process/list grid
 *
 * @category    Magento
 * @package     Magento_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Index\Block\Adminhtml\Process\Grid;

class Massaction extends \Magento\Adminhtml\Block\Widget\Grid\Massaction\AbstractMassaction
{
    /**
     * Get ids for only visible indexers
     *
     * @return string
     */
    public function getGridIdsJson()
    {
        if (!$this->getUseSelectAll()) {
            return '';
        }

        $ids = array();
        foreach ($this->getParentBlock()->getCollection() as $process) {
            $ids[] = $process->getId();
        }

        return implode(',', $ids);
    }
}
