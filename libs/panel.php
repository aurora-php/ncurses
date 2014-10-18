<?php

/*
 * This file is part of the 'org.octris.ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\ncurses {
    /**
     * Ncurses panel handling.
     *
     * @octdoc      c:component/panel
     * @copyright   copyright (c) 2013-2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class panel
    /**/
    {
        /**
         * Resources to handle.
         *
         * @octdoc  p:panel/$resources
         * @type    array
         */
        protected $resources;
        /**/

        /**
         * Whether panel is visible.
         *
         * @octdoc  p:panel/$is_visible
         * @type    bool
         */
        protected $is_visible = true;
        /**/

        /**
         * Constructor, panel is a static class.
         *
         * @octdoc  m:panel/__construct
         * @param   array                       $resources              One or multiple resources to handle.
         */
        public function __construct(array $resources)
        /**/
        {
            foreach ($resources as $resource) {
                $this->resources[] = ncurses_new_panel($resource);
            }
            
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
            foreach ($this->resources as $resource) {
                ncurses_del_panel($resource);
            }
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
                foreach ($this->resources as $resource) {
                    ncurses_show_panel($resource);
                    ncurses_update_panels();
                }

                ncurses_doupdate();
                $this->is_visible = true;
            } else {
                // $this->focus();
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

            foreach ($this->resources as $resource) {
                ncurses_hide_panel($resource);
                ncurses_update_panels();
            }

            ncurses_doupdate();

            $this->is_visible = false;
        }
    }
}
