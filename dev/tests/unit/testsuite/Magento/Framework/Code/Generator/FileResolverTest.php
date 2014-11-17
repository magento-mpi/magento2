<?php
/**
 * Unit test for \Magento\Framework\Code\Generator\FileResolver
 *
 * Only one method is unit testable, other methods require integration testing.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Code\Generator;

use \Magento\TestFramework\Helper\ObjectManager;

class FileResolverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Framework\Code\Generator\FileResolver
     */
    protected $model;

    public function setUp()
    {
        $this->model = (new ObjectManager($this))->getObject('Magento\Framework\Code\Generator\FileResolver');
    }

    public function testGetFilePath()
    {
        $this->assertSame('Path/To/My/Class.php', $this->model->getFilePath('Path\To\My_Class'));
    }
}
