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
         * Window resource.
         *
         * @octdoc  p:window/$window_resource
         * @var     resource
         */
        protected $window_resource;
        /**/

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
         * Constructor. If the X and/or Y position of the window is not set, the window will get rendered
         * centered vertically and/or horizontally on the screen.
         *
         * @octdoc  m:window/__construct
         * @param   int             $width          Width of window.
         * @param   int             $height         Height of window.
         * @param   int             $x              Optional x position of window.
         * @param   int             $y              Optional y position of window.
         */
        public function __construct($width, $height, $x = null, $y = null)
        /**/
        {
            ncurses_getmaxyx(STDSCR, $max_y, $max_x);

            $this->width  = min($width, $max_x);
            $this->height = min($height, $max_y);

            $this->x = (is_null($x)
                        ? floor(($max_x - $this->width) / 2)
                        : $x);
            $this->y = (is_null($y)
                        ? floor(($max_y - $this->height) / 2)
                        : $y);
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
            ncurses_delwin($this->window_resource);
        }

        /**
         * Get size of window.
         *
         * @octdoc  m:window/getSize
         * @return  stdClass                                            Size ->width, ->height
         */
        public function getSize()
        /**/
        {
            return (object)array(
                'width'  => $this->width, 
                'height' => $this->height
            );
        }

        /**
         * Get inner size of window.
         *
         * @octdoc  m:window/getInnerSize
         * @return  stdClass                                            Size ->width, ->height
         */
        public function getInnerSize()
        /**/
        {
            return (object)array(
                'width'  => $this->width - 2 * (int)$this->has_border, 
                'height' => $this->height - 2 * (int)$this->has_border
            );
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
            if ($this->has_border) {
                // two windows to prevent overwriting of window border
                $this->window_resource = ncurses_newwin($this->height, $this->width, $this->y, $this->x);
                $this->resource = ncurses_newwin($this->height - 2, $this->width - 2, $this->y + 1, $this->x + 1);

                ncurses_wborder($this->window_resource, 0, 0, 0, 0, 0, 0, 0, 0);
            } else {
                $this->window_resource = ncurses_newwin($this->height, $this->width, $this->y, $this->x);
                $this->resource = $this->window_resource;
            }
            
            parent::build();

            $this->panel = new \org\octris\ncurses\panel($this, $this->window_resource);

            $this->is_build = true;
        }

        /**
         * Refresh window.
         *
         * @octdoc  m:window/refresh
         */
        public function refresh()
        /**/
        {
            ncurses_wrefresh($this->window_resource);

            parent::refresh();
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
    }
}
