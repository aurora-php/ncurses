<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\ncurses {
    /**
     * Ncurses application class.
     *
     * @octdoc      c:core/app
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class app
    /**/
    {

    }

    // initialize initialization and ending
    ncurses_init();

    register_shutdown_function(function() {
    	ncurses_end();
    });
}
