<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_PrintedTemplate_Model_Tax_CompoundIdTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test addAfter method
     */
    public function testAddAfter()
    {
        $model = new Saas_PrintedTemplate_Model_Tax_CompoundId();

        $model->addAfter(1);
        $this->assertEquals(array(1), $model->toArray());

        $model->addAfter(2);
        $this->assertEquals(array(1, 2), $model->toArray());

        $model->addAfter(3);
        $this->assertEquals(array(1, 2, 3), $model->toArray());
    }

    /**
     * Test addAnd method
     */
    public function testAddAnd()
    {
        $model = new Saas_PrintedTemplate_Model_Tax_CompoundId();

        $model->addAnd(1);
        $this->assertEquals(array(1), $model->toArray());

        $model->addAnd(2);
        $this->assertEquals(array(array(1, 2)), $model->toArray());

        $model->addAnd(3);
        $this->assertEquals(array(array(1, 2, 3)), $model->toArray());
    }

    /**
     * Test both addAnd and addAfter methods in combination
     * Also test toArray method
     */
    public function testAddAfterAnd()
    {
        $model = new Saas_PrintedTemplate_Model_Tax_CompoundId();

        $model->addAfter(1);
        $this->assertEquals(array(1), $model->toArray());

        $model->addAnd(2);
        $this->assertEquals(array(array(1, 2)), $model->toArray());

        $model->addAfter(3);
        $this->assertEquals(array(array(1, 2), 3), $model->toArray());

        $model->addAfter(4);
        $this->assertEquals(array(array(1, 2), 3, 4), $model->toArray());

        $model->addAnd(5);
        $this->assertEquals(array(array(1, 2), 3, array(4, 5)), $model->toArray());

        $model->addAnd(array(6,7));
        $this->assertEquals(array(array(1, 2), 3, array(4, 5, array(6, 7))), $model->toArray());

        $model->addAfter(array(8, 9));
        $this->assertEquals(array(array(1, 2), 3, array(4, 5, array(6, 7)), array(8, 9)), $model->toArray());
    }

    /**
     * Test toString method
     */
    public function testToString()
    {
        $model = new Saas_PrintedTemplate_Model_Tax_CompoundId();

        $and = Saas_PrintedTemplate_Model_Tax_CompoundId::AND_SEPARATOR;
        $after = Saas_PrintedTemplate_Model_Tax_CompoundId::AFTER_SEPARATOR;

        $model->addAfter(1);
        $this->assertEquals('1', $model->toString());

        $model->addAfter(2);
        $this->assertEquals('1' . $after . '2', $model->toString());

        $model->addAnd(1);
        $this->assertEquals('1' . $after . '2' . $and . '1', $model->toString());

        $model->addAnd('simplestring');
        $this->assertEquals('1' . $after . '2' . $and . '1' . $and . 'simplestring', $model->toString());
    }
}
