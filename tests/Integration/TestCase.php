<?php

namespace DayRev\Smmry\Tests\Integration;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected $config = [];

    public function __construct()
    {
        parent::__construct();
        $this->parseParams();
    }

    protected function parseParams()
    {
        $params = $_SERVER['argv'];
        foreach ($params as $index => $param)
        {
            if ($param != '-d') {
                continue;
            }

            if (!$config = $params[$index + 1] ?? false) {
                continue;
            }

            if (strpos($config, '=') === false) {
                continue;
            }

            list($key, $value) = explode('=', $config);
            $this->config[$key] = $value;
        }

        // Ensure 'api_key' is set
        if (!isset($this->config['api_key'])) {
            $this->config['api_key'] = 'FD66E4690D'; // Default or test value
        }
    }
}
