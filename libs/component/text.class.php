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
     * Floating text widget.
     *
     * @octdoc      c:component/text
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class text extends \org\octris\ncurses\component
    /**/
    {
        /**
         * Text alignment.
         *
         * @octdoc  d:text/T_...
         */
        const T_LEFT    = STR_PAD_RIGHT;
        const T_RIGHT   = STR_PAD_LEFT;
        const T_CENTER  = STR_PAD_BOTH;
        const T_JUSTIFY = 3;
        /**/

        /**
         * Text.
         * 
         * @octdoc  p:text/$text
         * @var     string
         */
        protected $text;
        /**/

        /**
         * Text alignment.
         *
         * @octdoc  p:text/$align
         * @var     int
         */
        protected $align;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:text/__construct
         * @param   string                          $text           Text to display.
         * @param   int                             $align          Optional text alignment.
         */
        public function __construct($text, $align = self::T_LEFT)
        /**/
        {
            $this->text  = $text;
            $this->align = $align;
        }

        /**
         * Render text.
         *
         * @octdoc  m:text/render
         */
        public function build()
        /**/
        {
            $size = $this->parent->getInnerSize();

            // text formatting
            $rows  = explode("\n", wordwrap($this->text, $size->width));

            if ($this->align != self::T_JUSTIFY) {
                array_walk($rows, function(&$row) use ($size) {
                    $row = str_pad($row, $size->width, ' ', $this->align);
                });

                $text = implode("", $rows);
            } else {
                // TODO: justify
                $text = implode("", $rows);
            }

            // output
            ncurses_mvwaddstr($this->parent->getResource(), 0, 0, $text);
        }
    }
}
