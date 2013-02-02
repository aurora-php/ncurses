<?php

/*
 * This file is part of the 'org.octris.ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\ncurses\container {
    /**
     * Windows container.
     *
     * @octdoc      c:container/window
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class window extends \org\octris\ncurses\container
    /**/
    {
        /**
         * Panel instance the window is assigned to.
         *
         * @octdoc  p:window/$panel
         * @var     \org\octris\ncurses\panel
         */
        private $panel;
        /**/

        /**
         * Whether window has a border.
         *
         * @octdoc  p:window/$has_border
         * @var     bool
         */
        protected $has_border = true;
        /**/

        /**
         * Whether window has been build.
         *
         * @octdoc  p:window/$is_build
         * @var     bool
         */
        protected $is_build = false;
        /**/

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
         * Destructor.
         *
         * @octdoc  m:window/__destruct
         */
        public function __destruct()
        /**/
        {
            unset($this->panel);

            ncurses_delwin($this->resource);
        }

        /**
         * Return whether window is visible.
         *
         * @octdoc  m:window/isVisible
         * @return  bool                                Returns whether window is visible.
         */
        public function isVisible()
        /**/
        {
            return $this->panel->isVisible();
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

            if ($this->has_border) {
                ncurses_wborder($this->resource, 0, 0, 0, 0, 0, 0, 0, 0);
            }
            
            $this->setup();

            parent::build();

            $this->panel = new \org\octris\ncurses\panel($this);

            $this->is_build = true;
        }

        /**
         * Event get's called when window is displayed.
         *
         * @octdoc  m:window/onShow
         */
        public function onShow()
        /**/
        {
        }

        /**
         * Show window.
         *
         * @octdoc  m:window/show
         */
        public function show()
        /**/
        {
            if (!$this->is_build) $this->build();

            $this->panel->show();

            $this->onShow();
            $this->run();
        }

        /**
         * Hide window.
         *
         * @octdoc  m:window/hide
         */
        public function hide()
        /**/
        {
            $this->panel->hide();
        }

        /**
         * Main loop.
         *
         * @octdoc  m:window/run
         */
        protected function run()
        /**/
        {
        }
    }
}
