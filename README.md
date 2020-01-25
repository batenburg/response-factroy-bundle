# Response factory bundle

Response factory for the Symfony Framework.

Build Status: [![Build Status](https://travis-ci.org/doctrine/DoctrineBundle.svg?branch=master)](https://travis-ci.org/batenburg/response-factory-bundle.svg?branch=master)

## What is Response factory Bundle?

The response factory bundle is a bundle with a response factory, to make clean code in your Symfony controllers.
It is inspired by the response factory from laravel.

## For who?

Everybody who loves clean code.

## Installation

Install with composer:
```
composer require batenburg/response-factory-bundle
```

Register the bundle, add the following line to `config/bundles.php`:
```
    Batenburg\ResponseFactoryBundle\ResponseFactoryBundle::class => ['all' => true],
```

## Usage

After the installation is completed, the ResponseFactoryInterface can be resolved by dependency injection.
Or through the container. It is highly recommended to use dependency injection.

An example::

    <?php
    
    namespace App\Controller;
    
    use Batenburg\ResponseFactoryBundle\Component\HttpFoundation\Contract\ResponseFactoryInterface;
    use Batenburg\ResponseFactoryBundle\Component\HttpFoundation\Response;
    
    class BrandController
    {
    
        /**
         * @var ResponseFactoryInterface
         */
        private $responseFactory;
    
        /**
         * @param ResponseFactoryInterface $responseFactory
         */
        public function __construct(ResponseFactoryInterface $responseFactory)
        {
            $this->responseFactory = $responseFactory;
        }
    
        /**
         * @return Response
         */
        public function create(): Response
        {
            return $this->responseFactory->render('brand/create.html.twig');
        }
    }

## License

The Caching Bundle is open-sourced software licensed under the [MIT license](LICENSE.md).
