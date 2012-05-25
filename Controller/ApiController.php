<?php

/*
 * This file is part of the Terrific Composer Bundle.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\ComposerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Terrific\ComposerBundle\Entity\Module;
use Terrific\ComposerBundle\Entity\Skin;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * ApiController.
 */
class ApiController extends Controller
{
    /**
     * Creates Terrific components.
     *
     * @Route("/api/create", name="composer_api_create")
     * @Template()
     *
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        /*
        // make sure that only post request passes
        if ($request->getMethod() == 'POST') {

            // parse json

            // loop through json modules
            foreach ($jsonModules as $jsonModule) {
                // setup a fresh module object
                $module = new Module();

                // fill it
                $module->setStyle('less');
                $module->setName($jsonModule->name);

                foreach($jsonModule->skins as $jsonSkin) {
                    // setup a fresh module object
                    $skin = new Skin();

                    // fill it
                    $skin->setStyle('less')
                    $skin->setName($jsonSkin->name);
                    $skin->setModule($module->getName());
                }

                // create the module in the filesystem
                try {
                    $moduleManager = $this->get('terrific.composer.module.manager');
                    $moduleManager->createModule($module);

                    $this->get('session')->setFlash('notice', 'Module ' . ucfirst($module->getName()) . ' created successfully');
                }
                catch (\Exception $e) {
                    $logger = $this->get('logger');
                    $logger->err($e->getMessage());

                    $this->get('session')->setFlash('notice', 'Module could not be created: ' . $e->getMessage());
                }

            }
        }

        return array('form' => $form->createView());
        */
    }
}
