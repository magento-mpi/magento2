<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\AnnotationsDefecator;

interface FileItemI
{
    /**
     * Returns content of item as string
     *
     * @return string
     */
    public function getContent();

    /**
     * Returns line item number in file
     *
     * @return int
     */
    public function getNumber();

    /**
     * Whether has requested line item
     *
     * @param int $number
     * @return bool
     */
    public function hasLineNumber($number);

    /**
     * Sets item number
     *
     * @param int $number
     * @return void
     */
    public function setNumber($number);
}
