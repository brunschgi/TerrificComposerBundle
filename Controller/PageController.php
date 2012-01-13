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
 * PageController.
 */
class PageController extends Controller
{

    /**
     * Creates a terrific page.
     *
     * @Route("/module/create", name="composer_create_page")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse|\Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function createAction(Request $request)
    {
        // setup a fresh skin object
        $module = new Page();

        // create form
        $form = $this->createForm(new PageType(), $module);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                // create the page in the filesystem
                return $this->redirect($this->generateUrl('composer_create_skin_success'));
            }
        }

        return array('form' => $form->createView());
    }
}
