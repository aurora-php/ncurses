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
     * Ncurses application class.
     *
     * @octdoc      c:core/app
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class app extends \org\octris\ncurses\container
    /**/
    {
        /**
         * Application instance.
         *
         * @octdoc  p:app/$instance
         */
        protected static $instance = null;
        /**/

        /**
         * Constructor, create root window.
         *
         * @octdoc  m:app/__construct
         */
        protected function __construct()
        /**/
        {
        }

        /**
         * Create an instance of the application.
         *
         * @octdoc  m:app/getInstance
         */
        public static function getInstance()
        /**/
        {
            if (is_null(self::$instance)) {
                self::$instance = new app();
            }

            return self::$instance;
        }

        /**
         * Build application window.
         *
         * @octdoc  m:app/build
         */
        public function build()
        /**/
        {
            ncurses_newwin(0, 0, 0, 0);

            $this->resource = STDSCR;

            parent::build();
        }

        /**
         * Initialize application.
         *
         * @octdoc  m:app/init
         */
        public static function init()
        /**/
        {
            static $initialized = false;

            if ($initialized) return;

            // optionally set error logging
            if (defined('NCURSES_LOG')) {
                touch(NCURSES_LOG);

                $log_error = function($no, $str, $file, $line, $context = null) {
                    file_put_contents(
                        NCURSES_LOG,
                        sprintf("%s:%d #%d -- %s\n\n", $file, $line, $no, $str),
                        FILE_APPEND
                    );
                };

                set_error_handler($log_error);
            }

            // initialize initialization and ending
            ncurses_init();

            register_shutdown_function(function() {
                if (defined('NCURSES_LOG') && !is_null($error = error_get_last())) {
                    $log_error($error['type'], $error['message'], $error['file'], $error['line']);
                }

                ncurses_end();
            });

            // additional keys initialization
            $keys = array(
                'NCURSES_KEY_LF'     => 10,
                'NCURSES_KEY_CR'     => 13,
                'NCURSES_KEY_ENTER'  => 13,
                'NCURSES_KEY_ESCAPE' => 27,
                'NCURSES_KEY_SPACE'  => 32
            );

            array_walk($keys, function($code, $name) {
                if (!defined($name)) {
                    define($name, $code);
                }
            });

            // ---
            $initialized = true;
        }
    }

    app::init();
}
