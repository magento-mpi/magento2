<?php

namespace Magento\Setup\Tests\Model\Form;

class LicenseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Setup\Model\Form\License     */
    protected $model;

    public function setUp()
    {
        $this->model = new \Magento\Setup\Model\Form\License();
    }

    public function testLicenseInstance()
    {
        $this->assertInstanceOf('Zend\Form\Form', $this->model);
    }
}
