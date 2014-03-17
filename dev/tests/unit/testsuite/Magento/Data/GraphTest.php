<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Data;

class GraphTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $nodes
     * @param array $relations
     * @expectedException \InvalidArgumentException
     * @dataProvider constructorErrorDataProvider
     */
    public function testConstructorError($nodes, $relations)
    {
        new \Magento\Data\Graph($nodes, $relations);
    }

    /**
     * @return array
     */
    public function constructorErrorDataProvider()
    {
        return array(
            'duplicate nodes' => array(
                array(1, 2, 2), array()
            ),
            'self-link' => array(
                array(1, 2), array(array(1, 2), array(2, 2))
            ),
            'broken reference "from"' => array(
                array(1, 2), array(array(1, 2), array(3, 1))
            ),
            'broken reference "to"' => array(
                array(1, 2), array(array(1, 2), array(1, 3))
            ),
        );
    }

    /**
     * \Exceptions are covered by testConstructorError()
     */
    public function testAddRelation()
    {
        $model = new \Magento\Data\Graph(array(1, 2, 3), array(array(1, 2), array(2, 3)));
        $this->assertEquals(array(1 => array(2 => 2), 2 => array(3 => 3)), $model->getRelations());
        $this->assertSame($model, $model->addRelation(3, 1));
        $this->assertEquals(array(1 => array(2 => 2), 2 => array(3 => 3), 3 => array(1 => 1)), $model->getRelations());
    }

    public function testGetRelations()
    {
        // directional case is covered by testAddRelation()

        // inverse
        $model = new \Magento\Data\Graph(array(1, 2, 3), array(array(1, 2), array(2, 3)));
        $this->assertEquals(
            array(2 => array(1 => 1), 3 => array(2 => 2)), $model->getRelations(\Magento\Data\Graph::INVERSE)
        );

        // non-directional
        $this->assertEquals(
            array(1 => array(2 => 2), 2 => array(1 => 1, 3 => 3), 3 => array(2 => 2)),
            $model->getRelations(\Magento\Data\Graph::NON_DIRECTIONAL)
        );
    }

    public function testFindCycle()
    {
        $nodes = array(1, 2, 3, 4);
        $model = new \Magento\Data\Graph($nodes, array(
            array(1, 2), array(2, 3), array(3, 4),
        ));
        $this->assertEquals(array(), $model->findCycle());

        $model = new \Magento\Data\Graph($nodes, array(
            array(1, 2), array(2, 3), array(3, 4), array(4, 2)
        ));
        $this->assertEquals(array(), $model->findCycle(1));
        $cycle = $model->findCycle();
        sort($cycle);
        $this->assertEquals(array(2, 2, 3, 4), $cycle);
        $this->assertEquals(array(3, 4, 2, 3), $model->findCycle(3));

        $model = new \Magento\Data\Graph($nodes, array(
            array(1, 2), array(2, 3), array(3, 4), array(4, 2), array(3, 1)
        ));
        //find cycles for each node
        $cycles = $model->findCycle(null, false);
        $this->assertEquals(
            array(
                array(1, 2, 3, 1),
                array(2, 3, 4, 2),
                array(3, 4, 2, 3),
                array(4, 2, 3, 4),
            ), $cycles);
    }

    public function testDfs()
    {
        $model = new \Magento\Data\Graph(array(1, 2, 3, 4, 5), array(array(1, 2), array(2, 3), array(4, 5)));

        // directional
        $this->assertEquals(array(1, 2, 3), $model->dfs(1, 3));
        $this->assertEquals(array(), $model->dfs(3, 1));
        $this->assertEquals(array(4, 5), $model->dfs(4, 5));
        $this->assertEquals(array(), $model->dfs(1, 5));

        // inverse
        $this->assertEquals(array(3, 2, 1), $model->dfs(3, 1, \Magento\Data\Graph::INVERSE));

        // non-directional
        $model = new \Magento\Data\Graph(array(1, 2, 3), array(array(2, 1), array(2, 3)));
        $this->assertEquals(array(), $model->dfs(1, 3, \Magento\Data\Graph::DIRECTIONAL));
        $this->assertEquals(array(), $model->dfs(3, 1, \Magento\Data\Graph::INVERSE));
        $this->assertEquals(array(1, 2, 3), $model->dfs(1, 3, \Magento\Data\Graph::NON_DIRECTIONAL));
    }
}
