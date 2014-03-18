<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\ObjectManager\Relations;


class CompiledTest extends \PHPUnit_Framework_TestCase
{

    public function testHas()
    {
        $relations = array('amazing' => 'yes');

        $model = new \Magento\ObjectManager\Relations\Compiled($relations);
        $this->assertEquals(true, $model->has('amazing'));
        $this->assertEquals(false, $model->has('fuzzy'));
    }

    public function testGetParents()
    {
        $relations = array('amazing' => 'parents');

        $model = new \Magento\ObjectManager\Relations\Compiled($relations);
        $this->assertEquals('parents', $model->getParents('amazing'));
    }
}