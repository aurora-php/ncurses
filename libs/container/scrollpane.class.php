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
     * Scrollpane container.
     *
     * @octdoc      c:container/scrollpane
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class scrollpane extends \org\octris\ncurses\container
    /**/
    {
        /**
         * Scrollpane resource.
         *
         * @octdoc  p:scrollpane/$window_resource
         * @var     resource
         */
        protected $scrollpane_resource;
        /**/

        /**
         * X position of scrollpane.
         *
         * @octdoc  p:scrollpane/$x
         * @var     int
         */
        protected $x;
        /**/

        /**
         * Y position of scrollpane.
         *
         * @octdoc  p:scrollpane/$y
         * @var     int
         */
        protected $y;
        /**/

        /**
         * Visible height of scrollpane.
         *
         * @octdoc  p:scrollpane/$height
         * @var     int
         */
        protected $height;
        /**/

        /**
         * Visible width of scrollpane.
         *
         * @octdoc  p:scrollpane/$width
         * @var     int
         */
        protected $width;
        /**/

        /**
         * Buffer size in rows.
         *
         * @octdoc  p:scrollpane/$buffer_size
         * @var     int
         */
        protected $buffer_size;
        /**/

        /**
         * Options.
         *
         * @octdoc  p:scrollpane/$options
         * @var     int
         */
        protected $options;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:scrollpane/__construct
         * @param   int             $x              X position of scrollpane.
         * @param   int             $y              Y position of scrollpane.
         * @param   int             $height         Visible height of scrollpane.
         * @param   int             $width          Visible width of scrollpane.
         * @param   int             $buffer_size    Optional buffer size in rows of scrollpane.
         * @param   int             $options        Optional additional options.
         */
        public function __construct($x, $y, $height, $width, $buffer_size = PHP_INT_MAX, $options = 0)
        /**/
        {
            $this->x = $x;
            $this->y = $y;

            $this->height = $height;
            $this->width  = $width;

            $this->buffer_size = $buffer_size;
            $this->options     = $options;
        }

        /**
         * Destructor.
         *
         * @octdoc  m:scrollpane/__destruct
         */
        public function __destruct()
        /**/
        {
            ncurses_delwin($this->resource);
            ncurses_delwin($this->scrollpane_resource);
        }

        /**
         * A scrollpane cannot have child components.
         *
         * @octdoc  m:scrollpane/addChild
         */
        public function addChild($child)
        /**/
        {
            throw new \Exception('A scrollpane cannot have child components!');
        }

        /**
         * Add a row of content.
         *
         * @octdoc  m:scrollpane/addRow
         */
        public function addRow($row)
        /**/
        {
            ncurses_mvwaddstr($this->resource, $this->height - 1, 0, $row . "\n"); 
            ncurses_wrefresh($this->resource);
        }

        /**
         * Nothing to setup.
         *
         * @octdoc  m:scrollpane/setup
         */
        protected function setup()
        /**/
        {
        }

        /**
         * Build scrollpane.
         *
         * @octdoc  m:scrollpane/build
         */
        public function build()
        /**/
        {
            parent::build();

            $this->scrollpane_resource = ncurses_newwin($this->height, $this->width + 1, $this->y, $this->x);
            $this->resource = ncurses_newwin($this->height, $this->width, $this->y, $this->x);

            // add scrolling
            ncurses_scrollok($this->resource, true);
            // ncurses_wsetscrreg($this->scrollpane, )

            ncurses_wattron($this->scrollpane_resource, NCURSES_A_REVERSE);

            ncurses_wmove($this->scrollpane_resource, 0, $this->width);
            ncurses_wvline($this->scrollpane_resource, NCURSES_ACS_UARROW, 1);
            ncurses_wmove($this->scrollpane_resource, 1, $this->width);
            ncurses_wvline($this->scrollpane_resource, NCURSES_ACS_CKBOARD, $this->height - 2);
            ncurses_wmove($this->scrollpane_resource, $this->height - 1, $this->width);
            ncurses_wvline($this->scrollpane_resource, NCURSES_ACS_DARROW, 1);
        }

        /**
         * Refresh scrollpane.
         *
         * @octdoc  m:scrollpane/refresh
         */
        public function refresh()
        /**/
        {
            ncurses_wrefresh($this->scrollpane_resource);

            parent::refresh();
        }
    }
}
