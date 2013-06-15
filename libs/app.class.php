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
    abstract class app extends \org\octris\ncurses\container
    /**/
    {
        /**
         * Enable logging to file.
         *
         * @octdoc  p:app/$logging
         * @var     string|bool
         */
        protected static $logging = false;
        /**/

        /**
         * Application instance.
         *
         * @octdoc  p:app/$instance
         */
        protected static $instance = null;
        /**/

        /**
         * Application title.
         *
         * @octdoc  p:app/$title = '';
         * @var     string
         */
        protected $title = '';
        /**/

        /**
         * Set-up proxies.
         *
         * @octdoc  p:app/$proxies
         * @var     array
         */
        protected $proxies = array();
        /**/

        /**
         * Whether method 'leave' has been called.
         *
         * @octdoc  p:app/$left
         * @var     bool
         */
        protected $left = false;
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
         * Enable logging.
         *
         * @octdoc  m:app/enableLog
         * @para    string          $log_file               File to log to.
         */
        public static function enableLog($log_file)
        /**/
        {
            self::$logging = $log_file;
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
                self::$instance = new static();
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

            if ($this->title != '') {
                list($width, ) = $this->getMaxXY();

                ncurses_mvaddstr(0, 0, $this->title);
                ncurses_mvhline(1, 0, NCURSES_ACS_HLINE, $width);
            }

            parent::build();
        }

        /**
         * Proxy method.
         *
         * @octdoc  m:app/proxy
         * @return  mixed                                   Return value of callback function.
         */
        public function proxy(callable $cb)
        /**/
        {
            $hash = spl_object_hash($cb);

            if (!isset($this->proxies[$hash])) {
                $this->proxies[$hash] = $cb();
            }

            return $this->proxies[$hash];
        }

        /**
         * Main application loop to be implemented by application.
         *
         * @octdoc  a:app/main
         */
        abstract protected function main();
        /**/

        /**
         * Temporarly leave ncurses.
         *
         * @octdoc  m:app/leave
         * @return  bool                                    Returns true if application was able to leave ncurses.
         */
        public function leave()
        /**/
        {
            if (!$this->left && ($this->left = !ncurses_def_prog_mode())) {
                // ncurses_def_prog_mode returns false on success(!)
                ncurses_end();
            }

            return $this->left;
        }

        /**
         * Restore to ncurses.
         *
         * @octdoc  m:app/restore
         */
        public function restore()
        /**/
        {
            if ($this->left) {
                ncurses_reset_prog_mode();

                $this->refresh();

                $this->left = false;
            }
        }

        /**
         * Build and run application.
         *
         * @octdoc  m:app/run
         */
        public function run()
        /**/
        {
            // initialize ncurses and register shutdown function
            ncurses_init();
            ncurses_curs_set(0);
            ncurses_noecho();

            register_shutdown_function(function() {
                $error = error_get_last();

                if (!is_null($error)) {
                    static::logError($error['type'], $error['message'], $error['file'], $error['line']);
                }

                ncurses_end();
            });

            // render app UI
            $this->build();
            $this->refresh();

            // enter main loop
            $this->main();
        }

        /*
         *
         */

        /**
         * Error logger.
         *
         * @octdoc  m:app/logError
         * @param   int                     $no                     Number/type of error.
         * @param   string                  $message                Error message.
         * @param   string                  $file                   File the error occured in.
         * @param   int                     $line                   Number of line the error occured in.
         * @param   mixed                   $context                Optional context the error occured in.
         */
        public static function logError($no, $message, $file, $line, $context = null)
        /**/
        {
            if (!static::$logging || !is_writable(static::$logging)) return;

            file_put_contents(
                static::$logging,
                sprintf("type: %d\nfile: %s\nline: %d\nmsg : %s\n\n", $no, $file, $line, $message),
                FILE_APPEND
            );
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

            // set error logging
            set_error_handler(function($no, $message, $file, $line, $context = null) {
                static::logError($no, $message, $file, $line, $context);
            });

            // additional keys initialization
            $keys = array(
                'NCURSES_KEY_TAB'    =>  9,
                'NCURSES_KEY_LF'     => 10,
                'NCURSES_KEY_CR'     => 13,
                'NCURSES_KEY_ENTER'  => 13,
                'NCURSES_KEY_ESCAPE' => 27,
                'NCURSES_KEY_SPACE'  => 32,
                'NCURSES_KEY_BACK'   => 127
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
