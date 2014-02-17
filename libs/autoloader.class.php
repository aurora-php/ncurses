<?php

/*
 * This file is part of the 'org.octris.ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\ncurses {
    /**
     * Class Autoloader.
     *
     * @octdoc      c:app/autoloader
     * @copyright   copyright (c) 2010-2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class autoloader
    /**/
    {
        /**
         * Class Autoloader.
         *
         * @octdoc  m:app/autoload
         * @param   string      $classpath      Path of class to load.
         */
        public static function autoload($classpath)
        /**/
        {
            if (preg_match('/^\\\\?org\\\\octris\\\\ncurses\\\\/', $classpath, $match)) {
                $pkg = preg_replace('|\\\\|', '/', substr($classpath, strlen($match[0]))) . '.class.php';
                
                try {
                    include_once(__DIR__ . '/' . $pkg);
                } catch(\Exception $e) {
                }
            }
        }
    }

    spl_autoload_register(array('\org\octris\ncurses\autoloader', 'autoload'));
}