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
     * Label widget.
     *
     * @octdoc      c:component/label
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class label extends \org\octris\ncurses\component
    /**/
    {
        /**
         * X position of label.
         * 
         * @octdoc  p:label/$x
         * @var     int
         */
        protected $x;
        /**/

        /**
         * Y position of label.
         * 
         * @octdoc  p:label/$y
         * @var     int
         */
        protected $y;
        /**/

        /**
         * Text of label.
         * 
         * @octdoc  p:label/$text
         * @var     string
         */
        protected $text;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:label/__construct
         * @param   int                             $x              X position of label.
         * @param   int                             $y              Y position of label.
         * @param   string                          $text           Label text to display.
         */
        public function __construct($x, $y, $text)
        /**/
        {
            $this->x    = $x;
            $this->y    = $y;
            $this->text = $text;
        }

        /**
         * Render label.
         *
         * @octdoc  m:label/render
         */
        public function build()
        /**/
        {
            ncurses_mvwaddstr(
                $this->parent->getResource(), 
                $this->y, $this->x, $this->text
            );
        }
    }
}
