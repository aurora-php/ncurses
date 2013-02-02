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
     * Ncurses component class.
     *
     * @octdoc      c:ncurses/component
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class component
    /**/
    {
        /**
         * Resource of component.
         *
         * @octdoc  p:component/$resource
         * @var     resource|null
         */
        protected $resource = null;
        /**/

        /**
         * Parent container.
         *
         * @octdoc  p:component/$parent
         * @var     \org\octris\core\ncurses\container|null
         */
        protected $parent = null;
        /**/

        /**
         * Whether component can take the focus.
         *
         * @octdoc  p:component/$focusable
         * @var     bool
         */
        protected $focusable = true;
        /**/

        /**
         * Set parent container for component.
         *
         * @octdoc  m:component/setParent
         * @param   \org\octris\core\ncurses\container      $parent         Parent container.
         */
        final public function setParent(\org\octris\ncurses\container $parent)
        /**/
        {
            $this->parent = $parent;
        }

        /**
         * Get resource of component.
         *
         * @octdoc  m:component/getResource
         */
        public function getResource()
        /**/
        {
            return $this->resource;
        }

        /**
         * Return whether component can take the focus.
         *
         * @octdoc  m:component/isFocusable
         * @return  bool                                Whether component can take the focus.
         */
        public function isFocusable()
        /**/
        {
            return $this->focusable;
        }

        /**
         * Event is triggered if component loses focus.
         *
         * @octdoc  m:component/onBlur
         */
        public function onBlur()
        /**/
        {
        }

        /**
         * Event is triggered if component get's the focus.
         *
         * @octdoc  m:component/onFocus
         */
        public function onFocus()
        /**/
        {
        }

        /**
         * Set focus for component.
         *
         * @octdoc  m:component/focus
         */
        public function focus()
        /**/
        {
            $this->parent->focus($this);
        }

        /**
         * Render component.
         *
         * @octdoc  a:component/build
         */
        abstract public function build();
        /**/
    }
}
