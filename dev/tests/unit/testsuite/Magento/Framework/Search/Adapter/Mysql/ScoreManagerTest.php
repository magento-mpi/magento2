<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\TestFramework\Helper\ObjectManager;

class ScoreManagerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        (new ObjectManager($this))->getObject('Magento\Framework\Search\Adapter\Mysql\ScoreManager');
    }

    public function testGetScoreAlias()
    {
        /** @var \Magento\Framework\Search\Adapter\Mysql\ScoreManager $manager */
        $manager = (new ObjectManager($this))->getObject('Magento\Framework\Search\Adapter\Mysql\ScoreManager');

        $expectedResult = 'global_score';

        $this->assertEquals($expectedResult, $manager->getScoreAlias());
    }

    public function testHasQuery()
    {
        $queryName = 'query_name';

        /** @var \Magento\Framework\Search\Adapter\Mysql\ScoreManager $manager */
        $manager = (new ObjectManager($this))->getObject('Magento\Framework\Search\Adapter\Mysql\ScoreManager');

        $manager->setQueryBoost($queryName);

        $this->assertTrue($manager->hasQuery($queryName));
        $this->assertFalse($manager->hasQuery($queryName . '_test'));
    }

    public function testGetScoreQueryListSetQueryBoost()
    {
        $queryName = 'query_name';

        /** @var \Magento\Framework\Search\Adapter\Mysql\ScoreManager $manager */
        $manager = (new ObjectManager($this))->getObject('Magento\Framework\Search\Adapter\Mysql\ScoreManager');

        $manager->setQueryBoost($queryName, 2);

        $this->assertEquals([$queryName => ['boost' => 2]], $manager->getScoreQueryList());
    }

    public function testClear()
    {
        $queryName = 'query_name';

        /** @var \Magento\Framework\Search\Adapter\Mysql\ScoreManager $manager */
        $manager = (new ObjectManager($this))->getObject('Magento\Framework\Search\Adapter\Mysql\ScoreManager');

        $manager->setQueryBoost($queryName, 2);
        $manager->clear();

        $this->assertEquals([], $manager->getScoreQueryList());
    }

    public function testAddCondition()
    {
        $queryName = 'query_name';

        /** @var \Magento\Framework\Search\Adapter\Mysql\ScoreManager $manager */
        $manager = (new ObjectManager($this))->getObject('Magento\Framework\Search\Adapter\Mysql\ScoreManager');

        $manager->addCondition($queryName, 'someMatch', 3.2);
        $manager->setQueryBoost($queryName, 2.3);

        $expected = [
            'query_name' =>
                [
                    'boost' => 2.3,
                    'values' =>
                        [
                            [
                                'value' => 'someMatch',
                                'boost' => 3.2,
                            ],
                        ],
                ],
        ];

        $this->assertEquals($expected, $manager->getScoreQueryList());
    }

    public function testGetGeneratedCondition()
    {
        $queryName = 'query_name';
        $queryName2 = $queryName . '_2';

        /** @var \Magento\Framework\Search\Adapter\Mysql\ScoreManager $manager */
        $manager = (new ObjectManager($this))->getObject('Magento\Framework\Search\Adapter\Mysql\ScoreManager');

        $manager->setQueryBoost($queryName, 2.3);
        $manager->addCondition($queryName, 'someConditionOld', 1.5);
        $manager->addCondition($queryName, 'someConditionNew', 1.1);

        $manager->setQueryBoost($queryName2, 4.4);
        $manager->addCondition($queryName2, 'someCondition2Old', 4.2);
        $manager->addCondition($queryName2, 'someCondition2New', 4.7);

        $expected = '((someConditionOld * 1.5 + someConditionNew * 1.1) * 2.3 ' .
            '+ (someCondition2Old * 4.2 + someCondition2New * 4.7) * 4.4) AS global_score';

        $this->assertEquals($expected, $manager->getGeneratedCondition());
    }
}
