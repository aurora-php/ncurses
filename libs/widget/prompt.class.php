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
         * Y position to start line at.
         *
         * @octdoc  p:prompt/$line_y
         * @var     int
         */
        protected $line_y = 0;
        /**/

        /**
         * Y position of cursor.
         *
         * @octdoc  p:prompt/$cursor_y
         * @var     int
         */
        protected $cursor_y = 0;
        /**/

        /**
         * X position of cursor.
         *
         * @octdoc  p:prompt/$cursor_x
         * @var     int
         */
        protected $cursor_x = 0;
        /**/

        /**
         * Y position of cursor on screen.
         *
         + @octdoc  p:prompt/$cursor_sy
         * @var     int
         */
        protected $cursor_sy = 0;
        /**/

        /**
         * Relative Y position of cursor.
         *
         * @octdoc  p:prompt/$cursor_ry
         * @var     int
         */
        protected $cursor_ry = 0;
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
         * Length of prompt.
         *
         * @octdoc  p:prompt/$prompt_len
         * @var     int
         */
        protected $prompt_len;
        /**/

        /**
         * Width of container.
         *
         * @octdoc  p:prompt/$width
         * @var     int
         */
        protected $width;
        /**/

        /**
         * Height of container.
         *
         * @octdoc  p:prompt/$height
         * @var     int
         */
        protected $height;
        /**/

        /**
         * Maximum length a line can be displayed.
         *
         * @octdoc  p:prompt/$max_len
         * @var     int
         */
        protected $max_len;
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
            $this->prompt     = $prompt;
            $this->prompt_len = $this->cursor_x = strlen($prompt);
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

            ncurses_wmove($res, $this->cursor_y, $this->cursor_x);
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
         * @octdoc  m:prompt/render
         */
        public function build()
        /**/
        {
            parent::build();

            $res = $this->parent->getResource();

            ncurses_mvwaddstr($res, 0, 0, $this->prompt);
            ncurses_scrollok($res, true);

            $size = $this->parent->getInnerSize();
            $this->width  = $size->width;
            $this->height = $size->height;

            $this->max_len = $this->width * $this->height;
        }

        /**
         * Calculate cursor position.
         *
         * @octdoc  m:prompt/getCursorXY
         * @param   array                   $info                       Readline info.
         * @return  array                                               X, Y position.
         */
        protected function getCursorXY($info)
        /**/
        {
            $y = floor(($this->prompt_len + $info['point']) / $this->width);
            $x = ($this->prompt_len + $info['point']) % $this->width;

            $dy = $this->cursor_sy + ($y - $this->cursor_y);
            $sy = max(0, min($this->height - 1, $dy));

            $this->cursor_x  = $x;
            $this->cursor_y  = $y;
            $this->cursor_sy = $sy;

            trigger_error(sprintf("x,y: %d,%d | sy: %d | dy: %d", $x, $y, $sy, $dy));

            return array($x, $y, $sy);
        }

        /**
         * Main loop.
         *
         * @octdoc  m:prompt/run
         */
        protected function run()
        /**/
        {
            $res  = $this->parent->getResource();

            readline_callback_handler_install('', function($v) use ($res) {
                readline_add_history($v);

                // new input line
                $inc = ceil(($this->x + strlen($v)) / $this->width);

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
                    $line = $this->prompt . $info['line_buffer'] . str_repeat(' ', $this->width);

                    list($cx, $cy, $sy) = $this->getCursorXY($info);

                    // show line buffer contents and clear rest of line
                    ncurses_scrollok($res, false);
                    ncurses_mvwaddstr(
                        $res, 
                        $this->line_y, 
                        0, 
                        // $line
                        substr($line, ($cy - $sy) * $this->width, $this->max_len)
                    );
                    ncurses_wclrtoeol($res);
                    ncurses_scrollok($res, true);

                    trigger_error(substr($line, ($cy - $sy) * $this->width, $this->max_len));

                    // calculate and place cursor
                    ncurses_wmove($res, $sy, $cx);

                    // refresh window
                    ncurses_wrefresh($res);
                }
            } while(true);
        }
    }
}
