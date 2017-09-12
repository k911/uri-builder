<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class UriExamplesTest extends TestCase
{

    /**
     * Examples and theirs expected output
     * @return array
     */
    public function validExampleProvider(): array
    {
        return [
            'Uri Facade usage' => [
                'https://api.foo.bar/v1?api_token=Qwerty%21%20%40%23%24TYu#foobar',
                'facade',
            ],
            'Readme'           => [
                'https://api.foo.bar/v1?api_token=Qwerty%21%20%40%23%24TYu#foobar',
                'readme',
            ],
        ];
    }


    /**
     * @dataProvider validExampleProvider
     *
     * @param string $expected
     * @param string $example
     */
    public function testExamples(string $expected, string $example)
    {
        $path = sprintf('%s/../examples/%s.php', __DIR__, $example);
        $this->assertEquals(true, file_exists($path));

        $this->assertEquals(true, ob_start());
        require $path;
        $output = trim(ob_get_clean());

        $this->assertEquals($expected, $output);
    }
}
