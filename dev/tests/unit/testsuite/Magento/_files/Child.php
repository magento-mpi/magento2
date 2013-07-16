<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
require_once __DIR__ . '/Parent.php';
require_once __DIR__ . '/ChildInterface.php';

class Magento_Test_Di_Child extends Magento_Test_Di_Parent implements Magento_Test_Di_ChildInterface
{
}
