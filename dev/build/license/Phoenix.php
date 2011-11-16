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

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Osl.php';
/**
 * Phoenix OSL license information class
 *
 */
class Phoenix extends Osl
{
    /**
     * Prepare data for phpdoc attribute "copyright"
     *
     * @return string
     */
    public function getCopyright()
    {
        $year = date('Y');
        return "Copyright (c) {$year} Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)";
    }
}
