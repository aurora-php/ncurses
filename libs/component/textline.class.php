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
     * Textline component.
     *
     * @octdoc      c:component/textline
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class textline extends \org\octris\ncurses\component
    /**/
    {
        /**
         * X position of textline.
         * 
         * @octdoc  p:textline/$x
         * @var     int
         */
        protected $x;
        /**/

        /**
         * Y position of textline.
         * 
         * @octdoc  p:textline/$y
         * @var     int
         */
        protected $y;
        /**/

        /**
         * Size of textline.
         *
         * @octdoc  p:textline/$size
         * @var     int
         */
        protected $size;
        /**/

        /**
         * Value of textline.
         *
         * @octdoc  p:textline/$value
         * @var     mixed
         */
        protected $value = '';
        /**/

        /**
         * Curser position.
         *
         * @octdoc  p:textline/$cursor_x
         * @var     int
         */
        protected $cursor_x = 0;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:textline/__construct
         * @param   int             $x              X position of textline.
         * @param   int             $y              Y position of textline.
         * @param   int             $size           Size of textline.
         * @param   mixed           $value          Optional value to show in textline.
         */
        public function __construct($x, $y, $size, $value = '')
        /**/
        {
            $this->x     = $x;
            $this->y     = $y;
            $this->size  = $size;
            $this->value = substr($value . str_repeat(' ', $size), 0, $size);
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

            ncurses_wmove($res, $this->y, $this->x + $this->cursor_x);

            $this->parent->refresh();            
        }

        /**
         * Lose focus.
         *
         * @octdoc  m:textline/onBlur
         */
        public function onBlur()
        /**/
        {
        }

        /**
         * Get's called when ENTER key is pressed on a button.
         *
         * @octdoc  m:textline/onAction
         */
        public function onAction()
        /**/
        {
            $this->propagateEvent('action');
        }

        /**
         * Trigger action if ENTER key is pressed.
         *
         * @octdoc  m:textline/onKeypress
         * @param   int                 $key            Code of the key that was pressed.
         */
        public function onKeypress($key_code)
        /**/
        {
            $res = $this->parent->getResource();

            $cursor_x = $this->cursor_x;
            $size     = min(strlen(rtrim($this->value)) + 1, $this->size);

            if ($key_code == NCURSES_KEY_LEFT) {
                if ($cursor_x > 0) --$cursor_x;
            } elseif ($key_code == NCURSES_KEY_RIGHT) {
                if ($cursor_x < $size - 1) ++$cursor_x;
            } elseif ($key_code == NCURSES_KEY_BACK) {
                if ($cursor_x > 0) {
                    --$cursor_x;

                    $this->value = substr(
                        substr_replace(
                            $this->value, 
                            '', 
                            $cursor_x, 
                            1
                        ) . str_repeat(' ', $this->size), 
                        0, 
                        $this->size
                    );

                    ncurses_mvwaddstr($res, $this->y, $this->x, $this->value);
                }
            } elseif (ctype_print($key_code)) {
                $this->value = substr(
                    substr_replace(
                        $this->value, 
                        chr($key_code), 
                        $cursor_x, 
                        0
                    ), 
                    0, 
                    $this->size
                ); 

                ncurses_mvwaddstr($res, $this->y, $this->x, $this->value);

                if ($cursor_x < $this->size - 1) ++$cursor_x;
            }

            ncurses_wmove($res, $this->y, $this->x + $cursor_x);

            $this->cursor_x = $cursor_x;

            $this->parent->refresh();
        }
        
        /**
         * Build textline.
         *
         * @octdoc  m:textline/build
         */
        public function build()
        /**/
        {
            ncurses_mvwaddstr(
                $this->parent->getResource(),
                $this->y, $this->x, $this->value
            );
        }
    }
}
