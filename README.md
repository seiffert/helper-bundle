# SeiffertHelperBundle [![Build Status](https://travis-ci.org/seiffert/helper-bundle.png?branch=master)](https://travis-ci.org/seiffert/helper-bundle)

This bundle introduces a structure for helper objects.

## Concepts

After developing Symfony2 applications for quite some time, I stumbled upon different use cases for some kind of helper objects. One of them for instance are controllers: As soon as you stop to use `FrameworkBundle`'s default controller as base class for your controllers, you probably start writing code that replaces the helper functions in the default controller. This code will probably be required in most of your controllers. In this situation, you either create controller classes that all have a lot of dependencies that are injected by the DIC, you re-introduce a base controller class, or you employ some kind of helper objects.

This bundle solves this kind of situations by introducing helper objects that are managed by the dependency injection container (thus they are created lazily and will be reused) and are made available via a single helper object - the `HelperBroker`. One broker aggregates multiple helpers that are collected and configured during container compilation and proxies calls to helper methods to these helper objects. 

## Setup

Require the package via composer:

`composer.json`:

        "require": {
            ...
            "seiffert/helper-bundle": "*",
            ...
        }

Activate the bundle in your AppKernel:

`app/AppKernel.php`:

        public function registerBundles()
        {
            $bundles = array(
                ...
                new Seiffert\HelperBundle\SeiffertHelperBundle(),
                ...
            );
            ...
        }

## Usage

To configure helpers, you just have to implement your custom helper classes, add them to the DIC, and tag them like this:

    services:
        my.helper:
            class: My\Helper\MyHelper
            tags:
                - { name: seiffert.helper, broker: my.helper.broker }
        my.second.helper:
            class: My\Helper\MySecondHelper
            tags:
                - { name: seiffert.helper, broker: my.helper.broker }
        my.service.that.requires.help:
            class: My\Service\ThatRequiresHelpService
            arguments:
                - @my.helper.broker

The argument injected into `My\Service\ThatRequiresHelpService` is of type `Seiffert\HelperBundle\HelperBroker` and answers to all methods defined in your helper classes `My\Helper\MySecondHelper` and `My\Helper\MyHelper`. This is done by proxying these method calls to the actual helper classes using `__call`.
To further illustrate the usage of a helper broker using the same scenario as above, I will sketch out the mentioned classes:

**`My\Helper\MyHelper`:**

    <?php

    namespace My\Helper;

    use Seiffert\HelperBundle\HelperInterface;

    class MyHelper implements HelperInterface
    {
        public static function getHelperMethodNames()
        {
            return array('fooHelp');
        }
        
        public function fooHelp()
        {
            return 'foo';
        }
    }
    
**`My\Helper\MyHelper`:**

    <?php

    namespace My\Helper;

    use Seiffert\HelperBundle\HelperInterface;

    class MyHelper implements HelperInterface
    {
        public static function getHelperMethodNames()
        {
            return array('barHelp');
        }
        
        public function barHelp()
        {
            return 'bar';
        }
    }

**`My\Service\ThatRequiresHelpService`:**

    <?php
    
    namespace My\Service;
    
    use Seiffert\HelperBundle\HelperBroker;
    
    class ThatRequiresHelperService
    {
        /**
         * @var HelperBroker $h
         */
        private $h;
        
        public function __construct(HelperBroker $helper)
        {
            $this->h = $helper;
        }
        
        public function doYourJob()
        {
            return $this->h->fooHelp() . '|' . $this->h->barHelp();
        }
    }    

By using helper brokers, you can minimize the dependencies of your own services and group helper objects, that are required in similar situations. Examples of helper classes can be found in the related [SeiffertControllerHelperBundle](https://github.com/seiffert/controller-helper-bundle).

## Advanced Topics

### Helper classes not implementing `HelperInterface`

It is possible to use classes for helper objects that don't implement `Seiffert\HelperBundle\HelperInterface`. In this case, all public methods of such a class will be made available through the helper broker.

### Default broker

If you don't add the attribute `broker` to you helper service's tag, the helper is added to a broker defined as `seiffert.helper.broker`. 

### Register helper objects at multiple brokers

Helpers can be used by multiple brokers. To do so, just duplicate the helper's tag `seiffert.helper` and declare different brokers:

    services:
        my.helper:
            class: My\Helper\MyHelper
            tags:
                - { name: seiffert.helper, broker: my.helper.broker }
                - { name: seiffert.helper, broker: my.other.helper.broker }
