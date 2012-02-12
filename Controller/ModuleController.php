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

            // prepare the parameter for module rendering
            $template = $fullModule->getTemplateByName($template)->getPath();

            if($skins) {
                $skins = explode(',', $skins);
            }
            else {
                $skins = array();
            }
        }
        catch (Exception $e) {
            $logger = $this->get('logger');
            $logger->err($e->getMessage());

            $this->get('session')->setFlash('notice', 'Module could not be found: ' . $e->getMessage());
        }

        // decide whether to render the layout or not (ajax = without layout | default = with layout)
        if($request->isXmlHttpRequest()) {
            // render the module without layout
            return $this->render('TerrificComposerBundle:Module:details.ajax.html.twig', array('module' => $module, 'template' => $template, 'skins' => $skins));
        }
        else {
            // render the module with layout
            return $this->render('TerrificComposerBundle:Module:details.html.twig', array('module' => $module, 'template' => $template, 'skins' => $skins));
        }
    }

    /**
     * Creates a terrific module.
     *
     * @Route("/module/create", name="composer_create_module")
     * @Template()
     *
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        if ($this->get('session')->has('module')) {
            // get the last module from the session to fill some defaults for the new one
            $tmpModule = $this->get('session')->get('module');

            // setup a fresh module object
            $module = new Module();
            $module->setStyle($tmpModule->getStyle());
        }
        else {
            // setup a fresh module object
            $module = new Module();

            // fill it with some defaults
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
     * @param Request $request
     * @return Response
     */
    public function addskinAction(Request $request)
    {

        // setup a fresh skin and module object
        $skin = new Skin();
        $module = new Module();

        if ($this->get('session')->has('module')) {
            // get the last module from the session to fill some additional defaults for the new skin
            $tmpModule = $this->get('session')->get('module');
            $skin->setModule($tmpModule->getName());
            $skin->setStyle($tmpModule->getStyle());
        }
        else {
            // fill it with some defaults
            $skin->setStyle('less');
        }

        // create form
        $form = $this->createForm(new SkinType(), $skin);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                // create the skin in the filesystem
                try {
                    $moduleManager = $this->get('terrific.composer.module.manager');
                    $moduleManager->createSkin($skin);

                    $module->setStyle($skin->getStyle());
                    $module->setName($skin->getModule());
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
