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
use Terrific\ComposerBundle\Entity\Skin;
use Terrific\ComposerBundle\Form\ModuleType;
use Terrific\ComposerBundle\Form\SkinType;

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
     * @Route("/module/details/{module}/{template}/{skins}", defaults={"template" = null, "skins" = null}, name = "composer_module_details")
     * @Template()
     */
    public function detailsAction(Request $request, $module, $template, $skins)
    {
        $fullModule = null;

        try {
            $moduleManager = $this->get('terrific.composer.module.manager');
            $fullModule = $moduleManager->getModuleByName($module);

            if(!$template) {
                $templates = $fullModule->getTemplates();
                $template = array_shift($templates)->getPath();
            }

            if($skins) {
                $skins = explode(',', $skins);
            }
        }
        catch (Exception $e) {
            $logger = $this->get('logger');
            $logger->err($e->getMessage());

            $this->get('session')->setFlash('notice', 'Module could not be found: ' . $e->getMessage());
        }

        return array('module' => $module, 'template' => $template, 'skins' => $skins);
    }

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
        if ($this->get('session')->has('module')) {
            // get the last module from the session to fill some defaults for the new one
            $tmpModule = $this->get('session')->get('module');

            // setup a fresh module object
            $module = new Module();
            $module->setAuthor($tmpModule->getAuthor());
            $module->setStyle($tmpModule->getStyle());
        }
        else {
            // setup a fresh module object
            $module = new Module();

            // fill it with some defaults
            $author = ucfirst(@exec('whoami'));
            $author = $author ? $author : ucfirst(@exec('echo %USERNAME%'));
            $module->setAuthor($author);
            $module->setStyle('less');
        }

        // create form
        $form = $this->createForm(new ModuleType(), $module);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                // save the module in the session
                $this->get('session')->set('module', $module);

                // create the module in the filesystem
                try {
                    $moduleManager = $this->get('terrific.composer.module.manager');
                    $moduleManager->createModule($module);

                    $this->get('session')->setFlash('notice', 'Module ' . ucfirst($module->getName()) . ' created successfully');
                }
                catch (Exception $e) {
                    $logger = $this->get('logger');
                    $logger->err($e->getMessage());

                    $this->get('session')->setFlash('notice', 'Module could not be created: ' . $e->getMessage());
                }

                if ($request->get('addskin', false)) {
                    // redirect to the add skin form
                    return $this->redirect($this->generateUrl('composer_add_skin'));
                }
                else {
                    // redirect to the create module form
                    return $this->redirect($this->generateUrl('composer_create_module'));
                }
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * Adds a skin to the module.
     *
     * @Route("/module/addskin", name="composer_add_skin")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse|\Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function addskinAction(Request $request)
    {

        // setup a fresh skin object
        $skin = new Skin();

        if ($this->get('session')->has('module')) {
            // get the module from the session
            $module = $this->get('session')->get('module');

            // fill the skin with some defaults
            $skin->setModule($module->getName());
            $skin->setAuthor($module->getAuthor());
            $skin->setStyle($module->getStyle());
        }
        else {
            // setup a fresh module object
            $module = new Module();

            // fill it with some defaults
            $author = ucfirst(@exec('whoami'));
            $author = $author ? $author : ucfirst(@exec('echo %USERNAME%'));
            $skin->setAuthor($author);
            $skin->setStyle('less');
        }

        // create form
        $form = $this->createForm(new SkinType(), $skin);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                // create the skin in the filesystem
                $module->addSkin($skin);

                try {
                    $moduleManager = $this->get('terrific.composer.module.manager');
                    $moduleManager->createModule($module);

                    $this->get('session')->set('module', $module);
                    $this->get('session')->setFlash('notice', 'Skin ' . ucfirst($skin->getName()) . ' for Module ' . ucfirst($module->getName()) . ' created successfully');
                }
                catch (Exception $e) {
                    $logger = $this->get('logger');
                    $logger->err($e->getMessage());

                    $this->get('session')->setFlash('notice', 'Skin could not be created: ' . $e->getMessage());
                }

                return $this->redirect($this->generateUrl('composer_add_skin'));
            }
        }

        return array('form' => $form->createView());
    }
}
