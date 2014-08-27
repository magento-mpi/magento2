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
    public function testBuild()
    {
        $queryName = 'query_name';
        $queryName2 = $queryName . '_2';

        /** @var \Magento\Framework\Search\Adapter\Mysql\ScoreBuilder $manager */
        $manager = (new ObjectManager($this))->getObject('Magento\Framework\Search\Adapter\Mysql\ScoreBuilder');

        $manager->setQueryBoost($queryName, 2.3);
        $manager->addCondition($queryName, 'someConditionOld', 1.5);
        $manager->addCondition($queryName, 'someConditionNew', 1.1);

        $manager->setQueryBoost($queryName2, 4.4);
        $manager->addCondition($queryName2, 'someCondition2Old', 4.2);
        $manager->addCondition($queryName2, 'someCondition2New', 4.7);

        $expected = '((someConditionOld * 1.5 + someConditionNew * 1.1) * 2.3 ' .
            '+ (someCondition2Old * 4.2 + someCondition2New * 4.7) * 4.4) AS global_score';

        $this->assertEquals($expected, $manager->build());
    }
}
