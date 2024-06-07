<?php

namespace DayRev\Smmry\Tests\Unit;

use DayRev\Smmry\SDK;
use PHPUnit\Framework\TestCase;
use stdClass;

class SDKTest extends TestCase
{
    protected $sdk;
    protected $config = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->parseParams();
        $this->sdk = new SDK(['api_key' => $this->config['api_key'], 'summary_length' => 5]);
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

    public function testApiKeyIsInvalid()
    {
        $sdk = new SDK(['api_key' => 'INVALID1J3243N090', 'summary_length' => 5]);
        $response = $sdk->summarizeText($this->getTextToSummarize());

        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertTrue(property_exists($response, 'sm_api_error'), "Property 'sm_api_error' does not exist");
        $this->assertTrue(property_exists($response, 'sm_api_message'), "Property 'sm_api_message' does not exist");
        $this->assertEquals(1, $response->sm_api_error);
        $this->assertEquals('INVALID API KEY', $response->sm_api_message);
    }

    public function testSummarizesExpectedText()
    {
        $response = $this->sdk->summarizeText($this->getTextToSummarize());

        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertTrue(property_exists($response, 'sm_api_content'), "Property 'sm_api_content' does not exist");
        $this->assertEquals($this->getExpectedTextSummary()->sm_api_content, $response->sm_api_content);
    }

    public function testSummarizesExpectedUrlText()
    {
        $response = $this->sdk->summarizeUrl(
            'http://collegefootball.ap.org/article/mayfield-leads-oklahoma-35-19-sugar-bowl-win-over-auburn'
        );

        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertTrue(property_exists($response, 'sm_api_content'), "Property 'sm_api_content' does not exist");
        $this->assertEquals($this->getExpectedUrlTextSummary()->sm_api_content, $response->sm_api_content);
    }

    protected function getExpectedSummary(string $text): stdClass
    {
        $summary = new stdClass();
        $summary->sm_api_title = '';
        $summary->sm_api_content = $text;

        return $summary;
    }

    protected function getExpectedTextSummary(): stdClass
    {
        return $this->getExpectedSummary(
            file_get_contents(__DIR__ . '/../Data/summarized-text.txt')
        );
    }

    protected function getExpectedUrlTextSummary(): stdClass
    {
        return $this->getExpectedSummary(
            file_get_contents(__DIR__ . '/../Data/summarized-url.txt')
        );
    }

    protected function getTextToSummarize(): string
    {
        return file_get_contents(__DIR__ . '/../Data/full-text.txt');
    }
}
