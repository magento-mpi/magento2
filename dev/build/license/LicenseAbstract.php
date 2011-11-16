<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   build
 * @package    license
 * @copyright  Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

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
        return "Copyright (c) {$year} Magento Inc. (http://www.magentocommerce.com)";
    }

    /**
     * Prepare data for phpdoc attribute "license"
     *
     * @abstract
     * @return string
     */
    abstract public function getLink();
}
