<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Limitation_Model_Mage_Core_Model_WebsiteTest extends PHPUnit_Framework_TestCase
{

    /**
     * @magentoConfigFixture limitations/website 1
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Sorry, but you can't add any more websites with this account.
     */
    public function testSaveValidationLimitation()
    {
        $model = Mage::getModel('Mage_Core_Model_Website');
        $model->setData(
            array(
                'code'              => 'test_website',
                'name'              => 'test website',
                'default_group_id'  => 1,
            )
        );

        $model->save();
    }
}
