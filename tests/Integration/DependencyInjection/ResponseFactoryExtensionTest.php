<?php

namespace Batenburg\ResponseFactoryBundle\Test\Integration\DependencyInjection;

use Batenburg\ResponseFactoryBundle\Component\HttpFoundation\Contract\ResponseFactoryInterface;
use Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory;
use Batenburg\ResponseFactoryBundle\DependencyInjection\ResponseFactoryExtension;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Twig\Environment;

/**
 * @covers \Batenburg\ResponseFactoryBundle\DependencyInjection\ResponseFactoryExtension
 */
class ResponseFactoryExtensionTest extends TestCase
{

    /**
     * @covers \Batenburg\ResponseFactoryBundle\DependencyInjection\ResponseFactoryExtension
     * @throws Exception
     */
    public function testServiceWiring(): void
    {
        // Setup
        $container = $this->getContainer();
        $extension = new ResponseFactoryExtension();
        $container->registerExtension($extension);
        $extension->load([], $container);
        // Validate
        $this->assertTrue(
            $container->hasDefinition('batenburg.response_factory_bundle.component.http_foundation.response_factory')
        );
        $this->assertSame(
            ResponseFactory::class,
            $container
                ->getDefinition('batenburg.response_factory_bundle.component.http_foundation.response_factory')
                ->getClass()
        );
        $this->assertFalse(
            $container
                ->getDefinition('batenburg.response_factory_bundle.component.http_foundation.response_factory')
                ->isPublic()
        );
        $this->assertTrue($container->hasAlias(ResponseFactoryInterface::class));
        $this->assertSame(
            'batenburg.response_factory_bundle.component.http_foundation.response_factory',
            $container->getAlias(ResponseFactoryInterface::class)->__toString()
        );
        $this->assertTrue(
            $container->getAlias(ResponseFactoryInterface::class)->isPublic()
        );
    }

    /**
     * @return ContainerBuilder
     */
    private function getContainer() : ContainerBuilder
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.name' => 'app',
            'kernel.debug' => false,
            'kernel.cache_dir' => sys_get_temp_dir(),
            'kernel.environment' => 'test',
            'kernel.root_dir' => __DIR__ . '/../../../',
        ]));

        $container->setDefinition(
            'cache.system',
            (new Definition(ArrayAdapter::class))->setPublic(true)
        );
        $container->setDefinition(
            'cache.app',
            (new Definition(ArrayAdapter::class))->setPublic(true)
        );
        $container->setDefinition(
            'my_pool',
            (new Definition(ArrayAdapter::class))->setPublic(true)
        );
        $container->setDefinition(
            'twig',
            (new Definition(Environment::class))->setPublic(true)
        );

        return $container;
    }
}
