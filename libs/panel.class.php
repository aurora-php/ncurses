<?php

/*
 * This file is part of the 'org.octris.ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\ncurses {
    /**
     * Ncurses panel handling.
     *
     * @octdoc      c:component/panel
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class panel
    /**/
    {
        /**
         * Resource of panel.
         *
         * @octdoc  p:panel/$resource
         * @var     resource
         */
        protected $resource;
        /**/

        /**
         * Id of panel.
         *
         * @octdoc  p:panel/$panel_id
         * @var     int
         */
        protected $panel_id;
        /**/

        /**
         * Whether panel is visible.
         *
         * @octdoc  p:panel/$is_visible
         * @var     bool
         */
        protected $is_visible = true;
        /**/

        /**
         * Panel instances.
         *
         * @octdoc  p:panel/$panels
         * @var     array
         */
        protected static $panels = array();
        /**/

        /**
         * Panel counter will increase for every new created panel.
         *
         * @octdoc  p:panel/$panel_cnt
         * @var     int
         */
        protected static $panel_cnt;
        /**/

        /**
         * Constructor, panel is a static class.
         *
         * @octdoc  m:panel/__construct
         * @param   \org\octris\ncurses\container           $container          Container that is assigned to the panel.
         * @param   resource                                $resource           Resource of the container.
         */
        public function __construct(\org\octris\ncurses\container $container, $resource)
        /**/
        {
            $this->panel_id = self::$panel_cnt++;

            self::$panels[$this->panel_id] = $container;

            $this->resource  = ncurses_new_panel($resource);
            
            $this->hide();  // all panels are hidden by default
        }

        /**
         * Destructor.
         *
         * @octdoc  m:panel/__destruct
         */
        public function __destruct()
        /**/
        {
            ncurses_del_panel($this->resource);
            unset(self::$panels[$this->panel_id]);
        }

        /**
         * Return whether panel is visible.
         *
         * @octdoc  m:panel/isVisible
         * @return  bool                                Returns whether panel is visible.
         */
        public function isVisible()
        /**/
        {
            return $this->is_visible;
        }

        /**
         * Show panel.
         *
         * @octdoc  m:panel/show
         */
        public function show()
        /**/
        {
            if (!$this->is_visible) {
                ncurses_show_panel($this->resource);
                ncurses_update_panels();
                ncurses_doupdate();
    
                $this->is_visible = true;
            } else {
                $this->focus();
            }
        }

        /**
         * Hide panel.
         *
         * @octdoc  m:panel/hide
         */
        public function hide()
        /**/
        {
            if (!$this->is_visible) return;

            ncurses_hide_panel($this->resource);
            ncurses_update_panels();
            ncurses_doupdate();

            $this->is_visible = false;
        }
    }
}
