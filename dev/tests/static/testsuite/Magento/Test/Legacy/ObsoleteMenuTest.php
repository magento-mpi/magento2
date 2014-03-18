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
 * Legacy tests to find obsolete menu declaration
 */
namespace Magento\Test\Legacy;

class ObsoleteMenuTest extends \PHPUnit_Framework_TestCase
{
    public function testMenuDeclaration()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $menuFile
             */
            function ($menuFile) {
                $menuXml = simplexml_load_file($menuFile);
                $xpath = '/config/menu/*[boolean(./children) or boolean(./title) or boolean(./action)]';
                $this->assertEmpty(
                    $menuXml->xpath($xpath),
                    'Obsolete menu structure detected in file ' . $menuFile . '.'
                );
            },
            \Magento\TestFramework\Utility\Files::init()->getMainConfigFiles()
        );
    }
}
