<?php
/*
 * This file is part of the Terrific Composer Bundle.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\ComposerBundle\Util;

/**
 * StringUtils.
 */
class StringUtils {

    /**
     * Camelizes a dashed string.
     *
     * @param string $name A string to camelize
     * @return string The camelized string
     */
    static public function camelize($name)
    {
        return preg_replace_callback('/(^|-|\.)+(.)/', function ($match) { return ('.' === $match[1] ? '-' : '').strtoupper($match[2]); }, $name);
    }

    /**
     * Dashes a camelized string (e.g. mod.
     *
     * @param string $name A string to dash
     * @return string The dashed string
     */
    public static function dash($name) {
        return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1-\\2', '\\1-\\2'), $name));
    }
}