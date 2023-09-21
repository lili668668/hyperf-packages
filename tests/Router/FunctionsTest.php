<?php

declare(strict_types=1);

namespace SwooleTW\Hyperf\Tests\Router;

use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ContainerInterface;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SwooleTW\Hyperf\Router\UrlGenerator;

use function SwooleTW\Hyperf\Router\route;
use function SwooleTW\Hyperf\Router\secure_url;
use function SwooleTW\Hyperf\Router\url;

/**
 * @internal
 * @coversNothing
 */
class FunctionsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testRoute()
    {
        $urlGenerator = $this->mockUrlGenerator();

        $urlGenerator->shouldReceive('route')
            ->with('foo', ['bar'], 'http')
            ->andReturn('foo-bar');

        $urlGenerator->shouldReceive('route')
            ->with('foo', ['bar'], 'baz')
            ->andReturn('foo-bar-baz');

        $this->assertEquals('foo-bar', route('foo', ['bar']));
        $this->assertEquals('foo-bar-baz', route('foo', ['bar'], 'baz'));
    }

    public function testUrl()
    {
        $urlGenerator = $this->mockUrlGenerator();

        $urlGenerator->shouldReceive('to')
            ->with('foo', ['bar'], true)
            ->andReturn('foo-bar');

        $this->assertEquals('foo-bar', url('foo', ['bar'], true));
    }

    public function testSecureUrl()
    {
        $urlGenerator = $this->mockUrlGenerator();

        $urlGenerator->shouldReceive('secure')
            ->with('foo', ['bar'])
            ->andReturn('foo-bar');

        $this->assertEquals('foo-bar', secure_url('foo', ['bar']));
    }

    /**
     * @return MockInterface|UrlGenerator
     */
    private function mockUrlGenerator(): UrlGenerator
    {
        ! defined('BASE_PATH') && define('BASE_PATH', __DIR__);

        /** @var ContainerInterface|MockInterface */
        $container = Mockery::mock(ContainerInterface::class);
        $urlGenerator = Mockery::mock(UrlGenerator::class);

        $container->shouldReceive('get')
            ->with(UrlGenerator::class)
            ->andReturn($urlGenerator);

        ApplicationContext::setContainer($container);

        return $urlGenerator;
    }
}