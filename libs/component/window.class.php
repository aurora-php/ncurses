<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\ncurses\component {
    /**
     * Windows component.
     *
     * @octdoc      c:component/window
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class window extends \org\octris\ncurses\container
    /**/
    {
        /**
         * Width of window.
         * 
         * @octdoc  p:window/$width
         * @var     int
         */
        protected $width;
        /**/

        /**
         * Height of window.
         * 
         * @octdoc  p:window/$height
         * @var     int
         */
        protected $height;
        /**/

        /**
         * X position of window.
         * 
         * @octdoc  p:window/$x
         * @var     int
         */
        protected $x;
        /**/

        /**
         * Y position of window.
         * 
         * @octdoc  p:window/$y
         * @var     int
         */
        protected $y;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:window/__construct
         * @param   int             $width          Optional width of window (default: 0).
         * @param   int             $height         Optional height of window (default: 0).
         * @param   int             $x              Optional x position of window (default: 0).
         * @param   int             $y              Optional y position of window (default: 0).
         */
        public function __construct($width = 0, $height = 0, $x = 0, $y = 0)
        /**/
        {
            $this->width  = $width;
            $this->height = $height;
            $this->x      = $x;
            $this->y      = $y;
        }
        
        /**
         * Build window.
         *
         * @octdoc  m:window/build
         */
        public function build()
        /**/
        {
            $this->resource = ncurses_newwin($this->height, $this->width, $this->y, $this->x);
            ncurses_wborder($this->resource, 0, 0, 0, 0, 0, 0, 0, 0);
            
            parent::build();
        }
    }
}
