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
 * SearchController.
 */
class SearchController extends Controller
{

    /**
     * Displays the terrific component search.
     *
     * @Route("/search", name="composer_search")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse|\Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function searchAction(Request $request)
    {
        // get all modules
        $moduleManager =  $this->get('terrific.composer.module.manager');
        $modules = $moduleManager->getModules();

        // get all pages
        $pageManager =  $this->get('terrific.composer.page.manager');
        $pages = $pageManager->getPages();

        // merge the results
        $results = array_merge($modules, $pages);

        return array('results' => $results);
    }
}
