<?php

/*
 * This file is part of the Terrific Composer package.
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
use Terrific\ComposerBundle\Form\ModuleType;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * ModuleController.
 */
class ModuleController extends Controller
{

    /**
     * Creates a terrific module.
     *
     * @Route("/module/create", name="composer_create_module")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse|\Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function createAction(Request $request)
    {
        // setup a fresh module object
        $module = new Module();

        // fill it with some defaults
        $author = ucfirst(@exec('whoami'));
        $author = $author ? $author : ucfirst(@exec('echo %USERNAME%'));
        $module->setAuthor($author);

        // create form
        $form = $this->createForm(new ModuleType(), $module);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                // create the module in the filesystem
                $options = array('module' => $module->getName(), 'author' => $module->getAuthor(), 'style' => $module->getStyle());

                try {
                    $composerService =  $this->get('terrific.composer.module.manager');
                    $composerService->createModule($options);
                }
                catch (Exception $e) {
                    $logger = $this->get('logger');
                    $logger->err($e->getMessage());
                }

                $this->get('session')->setFlash('notice', 'Module '.$module->getName().' created successfully');
                return $this->redirect($this->generateUrl('composer_create_module_success'));
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * Displays the success message.
     *
     * @Route("/module/createsuccess", name="composer_create_module_success")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse|\Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function createsuccessAction(Request $request)
    {


    }
}