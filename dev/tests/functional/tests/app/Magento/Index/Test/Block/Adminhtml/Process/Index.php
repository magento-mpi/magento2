<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Index\Test\Block\Adminhtml\Process;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class Index
 * Index management grid
 *
 * @package Magento\Backend\Test\Block\Widget
 */
class Index extends Grid
{
    /**
     * Mass action for Reindex Data
     */
    public function reindexAll()
    {
        $this->massaction('Reindex Data');
    }
}