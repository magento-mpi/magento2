<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Model;

interface SimpleModelInterface
{
    /**
     * Convert simple model to array
     *
     * @return array
     */
    public function modelToArray();
}
