# TerrificComposerBundle

[![Build Status](https://secure.travis-ci.org/brunschgi/TerrificComposerBundle.png?branch=master)](http://travis-ci.org/brunschgi/TerrificComposerBundle)

The **TerrificComposer** bundle makes it easy to develop frontends based on the [Terrific Concept](http://terrifically.org).
It provides you several helpers and tools to streamline your frontend development.

The TerrificComposer bundle depends on the [TerrificCoreBundle](https://github.com/brunschgi/TerrificCoreBundle).
For installation of the TerrificCoreBundle, please follow the instructions [there](https://github.com/brunschgi/TerrificCoreBundle).


## Installation

TerrificComposerBundle can be conveniently installed via Composer. Just add the following to your composer.json file:

    // composer.json
    {
        // ...
        require: {
            // ...
            "brunschgi/terrific-composer-bundle": "dev-master"
        }
    }

Note: Please replace dev-master in the snippet above with the latest stable branch, for example 1.0.*. Please check the tags on Github for which versions are available.
Then, you can install the new dependencies by running Composer’s update command from the directory where your composer.json file is located:

    php composer.phar update

Now, Composer will automatically download all required files, and install them for you. All that is left to do is to update your AppKernel.php file, and register the new bundle:

    // in AppKernel::registerBundles()
    public function registerBundles()
    {
        return array(
            // ...
            new Terrific\ComposerBundle\TerrificComposerBundle(),
        );
    }


TerrificComposer creates a bundle for each of your Terrific module. To have them registered automatically, extend `app/AppKernel.php`:

    // register all terrific modules
    $dir = __DIR__.'/../src/Terrific/Module/';

    $finder = new Finder();
    $finder->directories()->in($dir)->depth('== 0');

    foreach ($finder as $file) {
        $filename = $file->getFilename();
        $module = 'Terrific\Module\\'.$filename.'\TerrificModule'.$filename;
        $bundles[] = new $module();
    }


Import the routing definition in `routing.yml`:

    # app/config/routing.yml
    TerrificComposerBundle:
        resource: "@TerrificComposerBundle/Controller/"
        type:     annotation
        prefix:   /terrific/composer


Enable the bundle's configuration in `app/config/config.yml` and `app/config/config_dev.yml`:

    # app/config/config.yml
    terrific_composer:
       composition_bundles: [@TerrificComposition] # optional|default=@TerrificComposition – the bundles where the controllers for your frontend lie
       module_layout: @TerrificComposition::base.html.twig # optional|default=@TerrificComposition::base.html.twig – the layout to take for the separate module view
       module_template: /path/to/your/module/template # optional|default=/path/to/TerrificComposerBundle/Template/Module -> extend or add additional module resources (ie. README.md)

    # app/config/config_dev.yml
    terrific_composer:
        toolbar: true # enables the composer toolbar in the dev environment


## Usage

To see the TerrificComposerBundle in action, download the [Terrific Composer Distribution](http://terrifically.org/composer)
and play around with the included examples. For more information about the Terrific Concept, please have a look at [http://terrifically.org](http://terrifically.org)

After that, the below should be pretty straight forward ;-)


### Terrific Composer Toolbar

The Toolbar provides you some useful helpers that helps you to streamline your frontend development.

#### Module / Skin Creation

![](https://github.com/brunschgi/TerrificComposerBundle/raw/master/Resources/doc/create.png)

Create module bundles (with or without skin) under /src/Terrific/Module/<moduleName>.

Notice: If you don't use the [Terrific Composer Distribution](http://terrifically.org/composer), you have to
register the created modules manually in `app/AppKernel.php`

The generated module structure contains the skeleton of the LESS/JavaScript files in [Terrific](http://terrifically.org)
manner, so that you can start right away.

#### Open Resources

![](https://github.com/brunschgi/TerrificComposerBundle/raw/master/Resources/doc/open.png)

The open dialog provides you quick access to your modules and pages. By clicking on a module you are able to implement
and test it isolated from the rest of your page. Furthermore you can play with different widths, templates and skins.

#### Inspect Mode

![](https://github.com/brunschgi/TerrificComposerBundle/raw/master/Resources/doc/inspect.png)

The inspect mode shows you the used modules on the current page.


### The Composer() annotation

The bundle provides an `Composer()` annotation for your controllers:

``` php
<?php

namespace Terrific\Composition\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Terrific\ComposerBundle\Annotation\Composer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Composer("Welcome")
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Composer("Examples Overview")
     * @Route("/examples", name="examples")
     * @Template()
     */
    public function examplesAction()
    {
        return array();
    }
}
```

The Composer annotation is used to enrich the open dialog with meaningful page names.


That's it… Enjoy!