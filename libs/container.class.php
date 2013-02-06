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
     * Container super class.
     *
     * @octdoc      c:ncurses/container
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class container
    /**/
    {
        use \org\octris\ncurses\event_tr;

        /**
         * Resource of container.
         *
         * @octdoc  p:container/$resource
         * @var     resource|null
         */
        protected $resource = null;
        /**/

        /**
         * Child components.
         *
         * @octdoc  p:container/$children
         * @var     array
         */
        protected $children = array();
        /**/

        /**
         * Stores instance of child that currently has the focus.
         *
         * @octdoc  p:container/$focused
         * @var     \org\octris\ncurses\component
         */
        protected $focused = null;
        /**/

        /**
         * Whether container has a border.
         *
         * @octdoc  p:container/$has_border
         * @var     bool
         */
        protected $has_border = false;
        /**/

        /**
         * Parent container.
         *
         * @octdoc  p:container/$parent
         * @var     \org\octris\core\ncurses\container|null
         */
        protected $parent = null;
        /**/

        /**
         * Whether to exit main loop. This property is modified using the ~doExit~ method and
         * will be queried by the main loop to exit the container.
         *
         * @octdoc  p:container/$do_exit
         * @var     array|bool
         */
        protected $do_exit = false;
        /**/

        /**
         * Set parent container for component.
         *
         * @octdoc  m:container/setParent
         * @param   \org\octris\core\ncurses\container      $parent         Parent container.
         */
        final public function setParent(\org\octris\ncurses\container $parent)
        /**/
        {
            $this->parent = $parent;
        }

        /**
         * Get size of container.
         *
         * @octdoc  m:container/getMaxXY
         * @return  array                           Returns an array of two values x, y.
         */
        public function getMaxXY()
        /**/
        {
            $this->refresh();
            ncurses_getmaxyx($this->resource, $y, $x);
            
            return array($x, $y);
        }

        /**
         * Get size of container.
         *
         * @octdoc  m:container/getSize
         * @return  stdClass                                            Size ->width, ->height
         */
        public function getSize()
        /**/
        {
            list($width, $height) = $this->getMaxXY();

            return (object)array(
                'width'  => $width, 
                'height' => $height
            );
        }

        /**
         * Get inner size of container. The size returned by this method differs from the
         * size returned by ~getSize~ if the container has a border.
         *
         * @octdoc  m:container/getInnerSize
         * @return  stdClass                                            Size ->width, ->height
         */
        public function getInnerSize()
        /**/
        {
            $size = $this->getSize();

            if ($this->has_border) {
                $size->width  -= 2;
                $size->height -= 2;
            }

            return $size;
        }

        /**
         * Get resource of container.
         *
         * @octdoc  m:container/getResource
         */
        public function getResource()
        /**/
        {
            return $this->resource;
        }

        /**
         * Set status for leaving container.
         *
         * @octdoc  m:container/doExit
         * @param   mixed                                                           $r_value            Value to return.
         */
        public function doExit($r_value)
        /**/
        {
            $this->do_exit = array('r_value' => $r_value);
        }

        /**
         * Add child component.
         *
         * @octdoc  m:container/addChild
         * @param   \org\octris\ncurses\component|\org\octris\ncurses\container       $child          Child component to add.
         * @return  \org\octris\ncurses\component|\org\octris\ncurses\container                       The instance of the child component.
         */
        public function addChild($child)
        /**/
        {
            if (!($child instanceof \org\octris\ncurses\container ||
                  $child instanceof \org\octris\ncurses\component)) {
                throw new \Exception('"\org\octris\ncurses\container" or "\org\octris\ncurses\component" expected');
            }

            $child->setParent($this);

            $this->children[] = $child;

            return $child;
        }

        /**
         * Setup container UI.
         *
         * @octdoc  a:container/setup
         */
        abstract protected function setup();
        /**/

        /**
         * Refresh container.
         *
         * @octdoc  m:container/refresh
         */
        public function refresh()
        /**/
        {
            ncurses_wrefresh($this->resource);

            foreach ($this->children as $child) {
                if ($child instanceof \org\octris\ncurses\container) {
                    $child->refresh();
                }
            }
        }

        /**
         * Set focus for a component in container.
         *
         * @octdoc  m:container/focus
         * @param   \org\octris\ncurses\component       $component          The component to focus.
         */
        public function focus(\org\octris\ncurses\component $component)
        /**/
        {
            // remove focus from component
            if (!is_null($this->focused)) {
                $this->focused->onBlur();
            }

            // set new focus
            $this->focused = $component;
            $this->focused->onFocus();

            $this->refresh();
        }

        /**
         * Move focus to next focusable component.
         *
         * @octdoc  m:container/moveFocus
         */
        public function moveFocus()
        /**/
        {
            $next = is_null($this->focused);
            $idx  = 0;
            $cnt  = count($this->children);

            while (true) {
                $child = $this->children[($idx++ % $cnt)];

                if (!$child->isFocusable()) continue;

                if ($next) {
                    $child->focus();
                    break;
                } elseif ($idx > $cnt) {
                    // nothing to focus
                    break;
                } else {
                    $next = ($child == $this->focused);
                }
            }
        }

        /**
         * Render component.
         *
         * @octdoc  m:container/build
         */
        public function build()
        /**/
        {
            $this->addKeyEvent(NCURSES_KEY_TAB, function() { $this->moveFocus(); });

            $this->setup();

            foreach ($this->children as $child) {
                $child->build();
            }
        }

        /**
         * Main loop.
         *
         * @octdoc  m:container/run
         */
        protected function run()
        /**/
        {
            do {
                $key_code = ncurses_getch($this->resource);

                if (!($propagate = is_null($this->focused))) {
                    $propagate = ($this->focused->propagateKeyEvent($key_code) && $this->do_exit === false);

                    $this->focused->onKeypress(null);
                }

                if ($propagate) {
                    $this->propagateKeyEvent($key_code);
                }
            } while($this->do_exit === false);

            $return = $this->do_exit['r_value'];
            $this->do_exit = false;

            return $return;
        }
    }
}
