<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Control;

use Magento\Framework\Object;

/**
 * Interface ControlInterface
 */
interface ControlInterface
{
    /**
     * @param Object $dataObject
     * @return string
     */
    public function render(Object $dataObject);
}
