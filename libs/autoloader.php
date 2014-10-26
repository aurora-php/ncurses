<?php

/*
 * This file is part of the 'octris/ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\ncurses;

/**
 * Class Autoloader.
 *
 * @octdoc      c:app/autoloader
 * @copyright   copyright (c) 2010-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class autoloader
{
    /**
     * Class Autoloader.
     *
     * @octdoc  m:autoloader/autoload
     * @param   string          $class              Class to load.
     */
    public static function autoload($class)
    {
        if (strpos($class, 'octris\\ncurses\\') === 0) {
            $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, 15)) . '.php';
            
            if (file_exists($file)) {
                require_once($file);
            }
        }
    }
}

spl_autoload_register(array('\octris\ncurses\autoloader', 'autoload'), true, true);

