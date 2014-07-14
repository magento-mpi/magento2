<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Review fixture
 *
 */
class Review extends DataFixture
{
    /**
     * Get review title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('fields/title/value');
    }

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                'nickname' => array(
                    'value' => 'Guest customer %isolation%',
                ),
                'title' => array(
                    'value' => 'Summary review %isolation%',
                ),
                'detail' => array(
                    'value' => 'Text review %isolation%',
                ),
            ),
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoReviewReview($this->_dataConfig, $this->_data);
    }
}
