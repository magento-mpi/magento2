<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig;

class DbConfig implements SegmentInterface
{
    /**
     * Keys for config.php
     */
    const KEY_HOST = 'host';
    const KEY_NAME = 'dbname';
    const KEY_USER = 'username';
    const KEY_PASS = 'password';
    const KEY_PREFIX = 'table_prefix';
    const KEY_MODEL = 'model';
    const KEY_INIT_STATEMENTS = 'initStatements';
    const KEY_ACTIVE = 'active';

    /**
     * Parameters used in setup tool
     */
    const KEY_DB_HOST = 'db_host';
    const KEY_DB_NAME = 'db_name';
    const KEY_DB_USER = 'db_user';
    const KEY_DB_PASS = 'db_pass';
    const KEY_DB_PREFIX = 'db_prefix';
    const KEY_DB_INIT_STATEMENTS = 'db_init_statements';
    const KEY_DB_MODEL = 'db_model';

    /**
     * Segment key
     */
    const CONFIG_KEY = 'db';

    /**
     * Data -- database connection
     *
     * @var array
     */
    private $data = [
        self::KEY_PREFIX => '',
        'connection' => [
            'default' => [
                'name' => 'default',
                self::KEY_HOST => '',
                self::KEY_NAME => '',
                self::KEY_USER => '',
                self::KEY_PASS => '',
                self::KEY_MODEL => 'mysql4',
                self::KEY_INIT_STATEMENTS => 'SET NAMES utf8;',
                self::KEY_ACTIVE => '1',
            ],
        ]
    ];


    /**
     * Constructor
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data)
    {
        $this->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return self::CONFIG_KEY;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Update data
     *
     * @param string[] $data
     * @return void
     */
    public function update($data)
    {
        $new = [];
        $new[self::KEY_PREFIX] = isset($data[self::KEY_PREFIX]) ?
            $data[self::KEY_PREFIX] : $this->data[self::KEY_PREFIX];
        foreach (array_keys($this->data['connection']['default']) as $key) {
            $new['connection']['default'][$key] =
                isset($data[$key]) ? $data[$key] : $this->data['connection']['default'][$key];
        }
        $this->checkData($new);
        $this->data = $new;
    }

    /**
     * Validate data
     *
     * @param array $data
     * @return void
     * @throws \InvalidArgumentException
     */
    private function checkData(array $data)
    {
        if (empty($data['connection']['default'][self::KEY_NAME])) {
            throw new \InvalidArgumentException('The Database Name field cannot be empty.');
        }
        $prefix = $data[self::KEY_PREFIX];
        if ($prefix != '') {
            $prefix = strtolower($prefix);
            if (!preg_match('/^[a-z]+[a-z0-9_]*$/', $prefix)) {
                throw new \InvalidArgumentException(
                    'The table prefix should contain only letters (a-z), numbers (0-9) or underscores (_); '
                    . 'the first character should be a letter.'
                );
            }
        }
    }
}
