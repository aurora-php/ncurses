<?php

/*
 * This file is part of the 'org.octris.ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\ncurses\component {
    /**
     * Shell component.
     *
     * @octdoc      c:component/shell
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class shell extends \org\octris\ncurses\container\scrollpane
    /**/
    {
        /**
         * X position to start input at.
         *
         * @octdoc  p:shell/$x
         * @var     int
         */
        protected $x;
        /**/

        /**
         * Y position to start input at.
         *
         * @octdoc  p:shell/$y
         * @var     int
         */
        protected $y;
        /**/

        /**
         * Prompt.
         * 
         * @octdoc  p:shell/$prompt
         * @var     string
         */
        protected $prompt;
        /**/

        /**
         * Cursor Y position.
         *
         * @octdoc  p:promt/$cursor_y;
         * @var     int
         */
        protected $cursor_y;
        /**/

        /**
         * Cursor X position.
         *
         * @octdoc  p:promt/$cursor_x;
         * @var     int
         */
        protected $cursor_x;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:shell/__construct
         * @param   int                             $x              X position to start input at.
         * @param   int                             $y              Y position to start input at.
         * @param   string                          $prompt         Optional prompt to display.
         */
        public function __construct($x, $y, $prompt = '')
        /**/
        {
            $this->x      = $x;
            $this->y      = $y;
            $this->prompt = $prompt;
        }

        /**
         * Get Focus.
         *
         * @octdoc  m:textline/onFocus
         */
        public function onFocus()
        /**/
        {
            $res = $this->parent->getResource();

            // ncurses_wmove($res, $this->y, $this->x + $this->cursor_x);
            ncurses_curs_set(1);

            $this->parent->refresh();            

            $this->run();
        }

        /**
         * Lose focus.
         *
         * @octdoc  m:textline/onBlur
         */
        public function onBlur()
        /**/
        {
            ncurses_curs_set(0);
        }

        /**
         * Render prompt.
         *
         * @octdoc  m:shell/render
         */
        public function build()
        /**/
        {
            $res  = $this->parent->getResource();
            $size = $this->parent->getInnerSize();

            $this->y = ($this->y < 0 ? $size->height - 1 : $this->y);

            ncurses_mvwaddstr($res, $y, 0, $this->prompt);

            $this->cursor_y = $y;
            $this->cursor_x = strlen($this->prompt);

            ncurses_scrollok($res, true);
        }

        /**
         * Newline.
         *
         * @octdoc  m:shell/

        /**
         * Main loop.
         *
         * @octdoc  m:shell/run
         */
        protected function run()
        /**/
        {
            $res = $this->parent->getResource();

            readline_callback_handler_install('', function($v) {
                ++$this->cursor_y;
            });

            readline_completion_function(function() {
            });

            do {
                $read   = array(STDIN);
                $write  = null;
                $except = null;
                
                $n = stream_select($read, $write, $except, null);

                if ($n && in_array(STDIN, $read)) {
                    readline_callback_read_char();

                    $info = readline_info();

                    ncurses_mvwaddstr(
                        $res,
                        $this->cursor_y, $this->cursor_x,
                        $info['line_buffer']
                    );
                    // ncurses_clrtoeol();
                    ncurses_wmove($res, $this->cursor_y, $this->cursor_x + $info['point']);
                    ncurses_wrefresh($res);
                }
            } while(true);
        }
    }
}
