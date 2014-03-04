<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payment interface
 */
namespace Magento\Payment\Model;

interface MethodInterface
{
    /**
     * Retrieve payment method code
     *
     * @return string
     */
    public function getCode();

    /**
     * Retrieve block type for method form generation
     *
     * @return string
     */
    public function getFormBlockType();

    /**
     * Retrieve payment method title
     *
     * @return string
     */
    public function getTitle();
}
