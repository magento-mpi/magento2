<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Tools\Migration\Acl\Db\Logger;

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Db/LoggerAbstract.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Db/Logger/File.php';

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructWithValidFile()
    {
        new \Magento\Tools\Migration\Acl\Db\Logger\File(realpath(dirname(__FILE__) . '/../../../../../') . '/tmp/');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructWithInValidFile()
    {
        new \Magento\Tools\Migration\Acl\Db\Logger\File(null);
    }
}

