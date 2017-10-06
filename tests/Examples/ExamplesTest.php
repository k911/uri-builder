<?php
declare(strict_types=1);

namespace Tests\Examples;

use PHPUnit\Framework\TestCase;

class ExamplesTest extends TestCase
{
    protected const EXAMPLES_PATH = __DIR__ . '/../../examples';

    /**
     * Examples and theirs expected output
     * @return array
     */
    public function validExampleProvider(): array
    {
        return [
            'UriBuilder facade usage' => [
                'facade',
                'https://user:password@api.foo.bar/v1?api_token=Qwerty%21%20%40%23%24TYu#foobar',
            ],
            'Readme'                  => [
                'readme',
                'https://user:password@api.foo.bar/v1?api_token=Qwerty%21%20%40%23%24TYu#foobar',
            ],
        ];
    }


    /**
     * @dataProvider validExampleProvider
     *
     * @param string $example
     */
    public function testExamplesExists(string $example)
    {
        $this->assertFileExists(sprintf('%s/%s.php', self::EXAMPLES_PATH, $example));
    }


    /**
     * @depends testExamplesExists
     * @dataProvider validExampleProvider
     *
     * @param string $example
     * @param string $expected
     */
    public function testExamples(string $example, string $expected)
    {
        $this->assertEquals(true, ob_start());
        require sprintf('%s/%s.php', self::EXAMPLES_PATH, $example);
        $output = trim(ob_get_clean());

        $this->assertEquals($expected, $output);
    }
}
