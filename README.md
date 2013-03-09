SeiffertHelperBundle
====================

This bundle introduces a structure for helper objects.

[![Build Status](https://travis-ci.org/seiffert/helper-bundle.png?branch=master)](https://travis-ci.org/seiffert/helper-bundle)

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
