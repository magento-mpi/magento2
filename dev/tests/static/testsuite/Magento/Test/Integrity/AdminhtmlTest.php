<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity;
use \Magento\TestFramework\Utility\Files;

class AdminhtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider decouplingDataProvider
     *
     * @param $file
     */
    public function testAdminhtmlDecoupling($file)
    {
        $blackList = $this->_getDecouplingBlackList();
        $blackList = array_map(
            function ($element) {
                return preg_quote($element, '/');
            },
            $blackList
        );
        $this->assertRegExp('/(' . implode('|', $blackList) . ')/', $file);
    }

    /**
     * @return array
     */
    public function decouplingDataProvider()
    {
        $pathToModule = Files::init()->getPathToSource()
            . '/app'
            . '/code'
            . '/Magento'
            . '/Adminhtml'
        ;

        $result = glob(
            $pathToModule . '/{Block,Controller,Helper,Model}/*',
            GLOB_BRACE | GLOB_NOSORT
        );
        // append views
        $result = array_merge($result, glob(
            $pathToModule . '/view[^layout]/adminhtml/*',
            GLOB_BRACE | GLOB_NOSORT
        ));
        // append layouts
        $result = array_merge($result, glob(
            $pathToModule . '/view/adminhtml/layout/*',
            GLOB_BRACE | GLOB_NOSORT
        ));

        return Files::composeDataSets($result);
    }

    /**
     * @return array
     */
    protected function _getDecouplingBlackList()
    {
        return require __DIR__ . '/_files/blacklist/adminhtml_decoupling.php';
    }
}
