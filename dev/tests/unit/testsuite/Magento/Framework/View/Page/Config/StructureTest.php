<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\Page\Config;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Test for page config structure model
 */
class StructureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Structure
     */
    protected $structure;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->structure = $this->objectManagerHelper->getObject(
            'Magento\Framework\View\Page\Config\Structure'
        );
    }

    protected function tearDown()
    {
        unset($this->structure);
    }

    public function testTitle()
    {
        $data = 'test';
        $this->structure->setTitle($data);
        $this->assertEquals($data, $this->structure->getTitle());
    }

    public function testMetadata()
    {
        $dataName = 'name';
        $dataContent = 'content';
        $expected = ['name' => 'content'];

        $this->structure->setMetadata($dataName, $dataContent);

        $this->assertEquals($expected, $this->structure->getMetadata());
    }

    public function testAssets()
    {
        $dataName = 'test';
        $dataAttributes = ['attr1', 'attr2'];
        $expected = [
            'test' => [
                'attr1',
                'attr2'
            ]
        ];

        $this->structure->addAssets($dataName, $dataAttributes);
        $this->assertEquals($expected, $this->structure->getAssets());
        $this->structure->removeAssets($dataName);
        $this->structure->processRemoveAssets();
        $this->assertEquals([], $this->structure->getAssets());
    }
}
