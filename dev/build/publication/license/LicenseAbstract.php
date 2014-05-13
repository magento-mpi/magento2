<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Tools\License;

/**
 * Interface for license information class
 *
 */
abstract class LicenseAbstract
{
    /**
     * Prepare short information about license
     *
     * @abstract
     * @return string
     */
    abstract public function getNotice();

    /**
     * Prepare data for phpdoc attribute "copyright"
     *
     * @return string
     */
    public function getCopyright()
    {
        $year = date('Y');
        return "Copyright (c) {$year} X.commerce, Inc. (http://www.magentocommerce.com)";
    }

    /**
     * Prepare data for phpdoc attribute "license"
     *
     * @abstract
     * @return string
     */
    abstract public function getLink();
}
