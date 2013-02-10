<?php

/*
 * This file is part of the 'org.octris.ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\ncurses\widget {
    /**
     * prompt component.
     *
     * @octdoc      c:component/prompt
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class prompt extends \org\octris\ncurses\widget
    /**/
    {
        /**
         * Y position to start input at.
         *
         * @octdoc  p:prompt/$y
         * @var     int
         */
        protected $y = 0;
        /**/

        /**
         * X position to start input at.
         *
         * @octdoc  p:prompt/$x
         * @var     int
         */
        protected $x = 0;
        /**/

        /**
         * Prompt text to show.
         * 
         * @octdoc  p:prompt/$prompt
         * @var     string
         */
        protected $prompt;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:prompt/__construct
         * @param   string                          $prompt         Optional prompt text to display.
         */
        public function __construct($prompt = '')
        /**/
        {
            $this->prompt = $prompt;
            $this->x      = strlen($prompt);
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

            ncurses_wmove($res, $this->y, $this->x);
            ncurses_curs_set(1);
            ncurses_scrollok($res, true);

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
         * @octdoc  m:prompt/render
         */
        public function build()
        /**/
        {
            parent::build();

            $res = $this->parent->getResource();

            ncurses_mvwaddstr($res, $this->y, 0, $this->prompt);
        }

        /**
         * Newline.
         *
         * @octdoc  m:prompt/

        /**
         * Main loop.
         *
         * @octdoc  m:prompt/run
         */
        protected function run()
        /**/
        {
            $res  = $this->parent->getResource();
            $size = $this->parent->getInnerSize();

            readline_callback_handler_install('', function($v) use ($res, $size) {
                // new input line
                $inc = ceil(($this->x + strlen($v)) / $size->width);

                $this->y += $inc;
                $this->x  = strlen($this->prompt);

                if ($this->y + $inc > $size->height) {
                    ncurses_wscrl($res, $inc);
                }

                ncurses_mvwaddstr($res, $this->y, 0, $this->prompt);
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
                        $this->y, $this->x,
                        $info['line_buffer']
                    );
                    // ncurses_clrtoeol();
                    ncurses_wmove($res, $this->y, $this->x + $info['point']);
                    ncurses_wrefresh($res);
                }
            } while(true);
        }
    }
}
