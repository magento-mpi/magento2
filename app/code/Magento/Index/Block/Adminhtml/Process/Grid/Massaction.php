<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mass-action block for process/list grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Index\Block\Adminhtml\Process\Grid;

class Massaction extends \Magento\Backend\Block\Widget\Grid\Massaction\Extended
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
