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
    class shell extends \org\octris\ncurses\container\window
    /**/
    {
        /**
         * Y position to start line at.
         *
         * @octdoc  p:shell/$line_y
         * @type    int
         */
        protected $line_y = 0;
        /**/

        /**
         * Y position of cursor.
         *
         * @octdoc  p:shell/$cursor_y
         * @type    int
         */
        protected $cursor_y = 0;
        /**/

        /**
         * X position of cursor.
         *
         * @octdoc  p:shell/$cursor_x
         * @type    int
         */
        protected $cursor_x = 0;
        /**/

        /**
         * Y position of cursor on screen.
         *
         + @octdoc  p:shell/$cursor_sy
         * @type    int
         */
        protected $cursor_sy = 0;
        /**/

        /**
         * Prompt text to show.
         * 
         * @octdoc  p:shell/$prompt
         * @type    string
         */
        protected $prompt;
        /**/

        /**
         * Length of prompt.
         *
         * @octdoc  p:shell/$prompt_len
         * @type    int
         */
        protected $prompt_len;
        /**/

        /**
         * Width of container.
         *
         * @octdoc  p:shell/$width
         * @type    int
         */
        protected $width;
        /**/

        /**
         * Height of container.
         *
         * @octdoc  p:shell/$height
         * @type    int
         */
        protected $height;
        /**/

        /**
         * Whether to exit main loop. This property is modified using the ~doExit~ method and
         * will be queried by the main loop to exit the prompt widget.
         *
         * @octdoc  p:shell/$do_exit
         * @type    array|bool
         */
        protected $do_exit = false;
        /**/

        /**
         * Is set to true if last key-press resulted in a newline.
         *
         * @octdoc  p:shell/$is_newline
         * @type    bool
         */
        protected $is_newline = false;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:shell/__construct
         * @param   int             $width          Width of window.
         * @param   int             $height         Height of window.
         * @param   string                          $prompt         Optional prompt text to display.
         * @param   int             $x              Optional x position of window.
         * @param   int             $y              Optional y position of window.
         */
        public function __construct($width, $height, $prompt = '', $x = null, $y = null)
        /**/
        {
            $this->prompt     = $prompt;
            $this->prompt_len = $this->cursor_x = strlen($prompt);

            parent::__construct($width, $height, $x, $y);
        }

        /**
         * Setup shell.
         *
         * @octdoc  m:shell/setup
         */
        protected function setup()
        /**/
        {
        }

        /**
         * Render prompt.
         *
         * @octdoc  m:shell/render
         */
        public function build()
        /**/
        {
            parent::build();

            $res = $this->getResource();

            ncurses_mvwaddstr($res, 0, 0, $this->prompt);
            ncurses_scrollok($res, true);

            $size = $this->getInnerSize();
            $this->width  = $size->width;
            $this->height = $size->height;
        }

        /**
         * Write a string in the shell window.
         *
         * @octdoc  p:shell/write
         * @param   string                  $str                    String to write.
         */
        public function write($str)
        /**/
        {
            // move cursor to next line if input row is not empty
            if (!$this->is_newline) {
                $info  = readline_info();

                if ($info['line_buffer'] != '') {
                    $this->doNewLine($info['line_buffer'], $info['point']);
                }
            }

            // write string
            $trimOne = function($str) {
                return (substr($str, -1) == "\n" 
                        ? substr($str, 0, -1)
                        : $str);
            };

            $res = $this->getResource();

            $rows1 = explode("\n", $trimOne($str));

            foreach ($rows1 as $row1) {
                $rows2 = explode("\n", $trimOne(chunk_split($row1, $this->width, "\n")));

                foreach ($rows2 as $row2) {
                    ncurses_mvwaddstr($res, $this->line_y, 0, $row2);

                    $this->doNewLine($row2);
                }
            }

            ncurses_mvwaddstr($res, $this->line_y, 0, $this->prompt);

            ncurses_wrefresh($res);
        }

        /**
         * Calculate and return max length the line to display can have.
         *
         * @octdoc  m:shell/getMaxLen
         * @return  int                                                 Maximum length.
         */
        protected function getMaxLen()
        /**/
        {
            return $this->width * ($this->height - $this->line_y);
        }

        /**
         * Calculate cursor position.
         *
         * @octdoc  m:shell/getCursorXY
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

            if ($y > $this->cursor_y && $this->line_y > 0 && $this->line_y + $sy > ($this->height - 1)) {
                $this->line_y--;

                ncurses_wscrl($this->getResource(), 1);
            }

            $this->cursor_x  = $x;
            $this->cursor_y  = $y;
            $this->cursor_sy = $sy;

            return array($x, $y, $sy);
        }

        /**
         * Get's called with after ENTER key is pressed.
         *
         * @octdoc  p:shell/onSubmit
         * @param   string                  $input                  Last input row.
         */
        protected function onSubmit($input)
        /**/
        {
        }

        /**
         * Get's called for TAB completion.
         *
         * @octdoc  p:shell/onCompletion
         * @param   string                  $input                  Current input row.
         */
        protected function onCompletion($input)
        /**/
        {
        }

        /**
         * Perform a newline.
         *
         * @octdoc  m:shell/doNewLine
         * @param   string                  $v                      String
         */
        public function doNewLine($v, $point = 0)
        /**/
        {
            $res = $this->getResource();

            // new input line
            $total  = ceil(($this->prompt_len + strlen($v)) / $this->width);
            $line_y = $this->line_y + $total;

            if ($line_y >= $this->height) {
                $scroll = max(1, $total - ((ceil(($this->prompt_len + $point) / $this->width) + $this->height - ($this->cursor_sy + 1)) - 1));
                ncurses_wscrl($res, $scroll);
                $line_y = $this->height - 1;

                $v = str_pad($this->prompt . $v, $total * $this->width);

                ncurses_mvwaddstr(
                    $res, 
                    max(0, $line_y - $total), 0, 
                    substr($v, -($total * $this->width))
                );
            }

            $this->line_y = $line_y;
        }

        /**
         * Main loop.
         *
         * @octdoc  m:shell/run
         */
        protected function run()
        /**/
        {
            $res   = $this->getResource();
            $point = 0;

            readline_callback_handler_install('', function($input) use (&$point) {
                readline_add_history($input);

                $this->doNewLine($input, $point);

                $this->is_newline = true;

                $input = trim($input);

                if ($input == 'quit' || $input == 'exit') {
                    $this->doExit();
                } else {
                    $this->onSubmit($input);
                }
            });
            
            readline_completion_function(function($input, $index) {
                $this->is_newline = false;

                $info  = readline_info();
                $input = substr($info['line_buffer'], 0, $info['end']);

                $this->onCompletion($input);
            });

            ncurses_wmove($res, $this->cursor_y, $this->cursor_x);
            ncurses_curs_set(1);

            do {
                $read   = array(STDIN);
                $write  = null;
                $except = null;
                
                $n = stream_select($read, $write, $except, null);

                if ($n && in_array(STDIN, $read)) {
                    readline_callback_read_char();

                    $this->is_newline = false;

                    $info  = readline_info();
                    $line  = $this->prompt . $info['line_buffer'] . str_repeat(' ', $this->width);
                    $point = $info['point'];

                    list($cx, $cy, $sy) = $this->getCursorXY($info);

                    // show line buffer contents and clear rest of line
                    ncurses_scrollok($res, false);
                    ncurses_mvwaddstr(
                        $res, 
                        $this->line_y, 
                        0, 
                        substr($line, ($cy - $sy) * $this->width, $this->getMaxLen())
                    );
                    ncurses_wclrtobot($res);
                    ncurses_scrollok($res, true);

                    // calculate and place cursor
                    ncurses_wmove($res, $this->line_y + $sy, $cx);

                    // refresh window
                    ncurses_wrefresh($res);
                }
            } while($this->do_exit === false);

            ncurses_curs_set(0);

            $return = $this->do_exit['r_value'];
            $this->do_exit = false;

            return $return;
        }
    }
}
