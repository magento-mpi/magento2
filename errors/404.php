<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Errors
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'processor.php';

$processor = new Error_Processor();
$processor->process404();
