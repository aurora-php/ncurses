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
         * Set value for textline.
         *
         * @octdoc  m:listbox/setValue
         * @param   mixed           $value          Value to set.
         */
        public function setValue($value)
        /**/
        {
            $this->value = substr($value . str_repeat(' ', $this->size), 0, $this->size);

            ncurses_mvwaddstr(
                $this->parent->getResource(),
                $this->y, $this->x, $this->value
            );

            $this->parent->refresh();
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
         * Get's called when ENTER key is pressed in textline.
         *
         * @octdoc  m:textline/onAction
         */
        public function onAction()
        /**/
        {
            $this->propagateEvent('action');
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

            // attach keyboard events
            $this->addKeyEvent(NCURSES_KEY_LEFT, function() {
                $size = min(strlen(rtrim($this->value)) + 1, $this->size);

                if ($this->cursor_x > 0) {
                    ncurses_wmove($this->parent->getResource(), $this->y, $this->x + --$this->cursor_x);
                    $this->parent->refresh();
                }
            });
            $this->addKeyEvent(NCURSES_KEY_RIGHT, function() {
                $size = min(strlen(rtrim($this->value)) + 1, $this->size);
                
                if ($this->cursor_x < $size - 1) {
                    ncurses_wmove($this->parent->getResource(), $this->y, $this->x + ++$this->cursor_x);
                    $this->parent->refresh();
                }
            });
            $this->addKeyEvent(NCURSES_KEY_BACK, function() {
                if ($this->cursor_x > 0) {
                    --$this->cursor_x;

                    $value = substr(substr_replace(
                        $this->value, 
                        '', 
                        $this->cursor_x, 
                        1
                    ) . str_repeat(' ', $this->size), 0, $this->size);

                    $this->setValue($value);

                    ncurses_wmove($this->parent->getResource(), $this->y, $this->x + $this->cursor_x);

                    $this->parent->refresh();
                }
            });
            $this->addKeyEvent(function($kc) { return ctype_print($kc); }, function($key_code) {
                $value = substr(substr_replace(
                    $this->value, 
                    chr($key_code), 
                    $this->cursor_x, 
                    0
                ), 0, $this->size);

                $this->setValue($value);

                if ($this->cursor_x < $this->size - 1) ++$this->cursor_x;

                ncurses_wmove($this->parent->getResource(), $this->y, $this->x + $this->cursor_x);

                $this->parent->refresh();
            });
        }
    }
}
