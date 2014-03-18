<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Legacy tests to find obsolete acl declaration
 */
namespace Magento\Test\Legacy;

class ObsoleteAclTest extends \PHPUnit_Framework_TestCase
{
    public function testAclDeclarations()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $aclFile
             */
            function ($aclFile) {
                $aclXml = simplexml_load_file($aclFile);
                $xpath = '/config/acl/*[boolean(./children) or boolean(./title)]';
                $this->assertEmpty(
                    $aclXml->xpath($xpath),
                    'Obsolete acl structure detected in file ' . $aclFile . '.'
                );
            },
            \Magento\TestFramework\Utility\Files::init()->getMainConfigFiles()
        );
    }
}
