<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Workaround for occasional non-zero exit code (exec returned: 139) caused by the PHP bug
 */
namespace Magento\TestFramework\Workaround;

class Segfault
{
    /**
     * Force garbage collection
     */
    public function endTestSuite()
    {
        gc_collect_cycles();
    }
}
