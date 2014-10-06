<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Session;

use \Magento\Backend\Model\SessionTest;

function session_name($name)
{
    SessionTest::assertEquals($name, 'adminhtml');
}
