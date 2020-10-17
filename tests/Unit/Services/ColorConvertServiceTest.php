<?php

namespace Services;

use App\Services\ColorConvertService;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Class ColorConvertServiceTest
 * @package Services
 * @coversDefaultClass \App\Services\ColorConvertService
 */
class ColorConvertServiceTest extends TestCase
{
    /** @var ColorConvertService|MockObject */
    public $service;

    /**
     * @param array $args
     * @param array $methods
     */
    public function getMockedService(array $args, array $methods = [])
    {
        $this->service = $this->getMockBuilder(ColorConvertService::class)
            ->setConstructorArgs($args)
            ->onlyMethods($methods)
            ->getMock();
    }

    /**
     * @test
     * @covers ::hexToRgba
     */
    function it_should_return_converted_rgba_value_from_hex_and_alpha() {
        $constructorArgs = ['#123123', '.4'];
        $expected = 'rgba(18,49,35,0.4)';

        $this->getMockedService($constructorArgs);

        $this->assertEquals($expected, $this->service->hexToRgba());
    }

    /**
     * @test
     * @covers ::convertAlphaToFloat
     */
    function it_should_return_void_when_alpha_is_valid()
    {
        $constructorArgs = ['#123123', '.3'];
        $methods = ['alphaIsValid'];

        $this->getMockedService($constructorArgs, $methods);

        $this->service
            ->expects($this->once())
            ->method('alphaIsValid')
            ->willReturn(true);

        $this->assertNull($this->service->convertAlphaToFloat());
    }

    /**
     * @test
     * @covers ::convertAlphaToFloat
     */
    function it_should_throw_exception_when_alpha_is_not_valid()
    {
        $constructorArgs = ['#123123', '.3'];
        $methods = ['alphaIsValid'];
        $this->expectException('Exception');

        $this->getMockedService($constructorArgs, $methods);

        $this->service
            ->expects($this->once())
            ->method('alphaIsValid')
            ->willReturn(false);

        $this->service->convertAlphaToFloat();
    }

    /**
     * @test
     * @covers ::alphaIsValid
     * @dataProvider alphaWrongValueDataProvider
     */
    function it_should_return_false_when_alpha_value_is_not_valid($alpha)
    {
        $constructorArgs = ['#123123', $alpha];

        $this->getMockedService($constructorArgs);

        $this->assertFalse($this->service->alphaIsValid());
    }

    /**
     * @test
     * @covers ::alphaIsValid
     * @dataProvider alphaRightValueDataProvider
     */
    function it_should_return_true_when_alpha_value_is_valid($alpha)
    {
        $constructorArgs = ['#123123', $alpha];

        $this->getMockedService($constructorArgs);

        $this->assertTrue($this->service->alphaIsValid());
    }

    /**
     * @test
     * @covers ::cleanHash
     */
    function it_should_return_hex_value_without_hash()
    {
        $constructorArgs = ['#123123', '.3'];
        $expected = '123123';

        $this->getMockedService($constructorArgs);

        $this->service->cleanHash();
        $this->assertEquals($expected, $this->getPrivateVariable($this->service, 'hex'));
    }

    /**
     * @test
     * @covers ::cleanHash
     */
    function it_should_return_hex_value()
    {
        $constructorArgs = ['123123', '.3'];
        $expected = '123123';

        $this->getMockedService($constructorArgs);

        $this->service->cleanHash();
        $this->assertEquals($expected, $this->getPrivateVariable($this->service, 'hex'));
    }

    /**
     * @test
     * @covers ::isValidHex
     * @covers ::isValidLength
     * @covers ::isValidDeclaration
     */
    function it_should_return_void_when_hex_value_is_right()
    {
        $constructorArgs = ['123123', '.3'];

        $this->getMockedService($constructorArgs);

        $this->assertNull($this->service->isValidHex());
    }

    /**
     * @test
     * @covers ::isValidHex
     * @covers ::isValidLength
     * @covers ::isValidDeclaration
     */
    function it_should_return_throw_exception_when_hex_value_is_different_three_or_six_character()
    {
        $constructorArgs = ['12312', '.3'];
        $this->expectException('Exception');

        $this->getMockedService($constructorArgs);

        $this->service->isValidHex();
    }

    /**
     * @test
     * @covers ::isValidHex
     * @covers ::isValidLength
     * @covers ::isValidDeclaration
     */
    function it_should_return_throw_exception_when_hex_value_declaration_is_not_valid()
    {
        $constructorArgs = ['123GGG', '.3'];
        $this->expectException('Exception');

        $this->getMockedService($constructorArgs);

        $this->service->isValidHex();
    }

    /**
     * @test
     * @covers ::convertHexToRgba
     */
    function it_should_return_rgba_string()
    {
        $constructorArgs = ['123123', '1'];
        $methods = ['hexToInteger'];
        $expected = 'rgba(1,1,1,1)';

        $this->getMockedService($constructorArgs,$methods);

        $this->service
            ->expects($this->exactly(3))
            ->method('hexToInteger')
            ->withConsecutive([1], [2], [3])
            ->willReturn(1);

        $this->assertEquals($expected, $this->service->convertHexToRgba());
    }

    /**
     * @test
     * @covers ::hexToInteger
     */
    function it_should_return_rgba_from_hex()
    {
        $constructorArgs = ['000000', '1'];
        $expected = 0;

        $this->getMockedService($constructorArgs);

        $this->assertEquals($expected, $this->service->hexToInteger(1));
    }

    /**
     * @test
     * @covers ::hexToInteger
     */
    function it_should_return_rgba_from_hex_when_hex_have_a_three_characters()
    {
        $constructorArgs = ['fff', '1'];
        $expected = 255;

        $this->getMockedService($constructorArgs);

        $this->assertEquals($expected, $this->service->hexToInteger(1));
    }

    /**
     * @return array
     */
    public function alphaRightValueDataProvider(): array
    {
        return [
            ['.3'],
            ['0.4'],
            [1],
            [0],
        ];
    }

    /**
     * @return array
     */
    public function alphaWrongValueDataProvider(): array
    {
        return [
            ['2'],
            [2],
            [-1],
        ];
    }
}
