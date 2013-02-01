<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\ncurses {
    /**
     * Container component.
     *
     * @octdoc      c:ncurses/container
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class container extends \org\octris\ncurses\component
    /**/
    {
        /**
         * Child components.
         *
         * @octdoc  p:container/$children
         * @var     array
         */
        protected $children = array();
        /**/

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
         * Add child component.
         *
         * @octdoc  m:container/addChild
         * @param   \org\octris\ncurses\component       $child          Child component to add.
         * @return  \org\octris\ncurses\component                       The instance of the child component.
         */
        public function addChild(\org\octris\ncurses\component $child)
        /**/
        {
            $child->setParent($this);

            $this->children[] = $child;

            return $child;
        }

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
         * Render component.
         *
         * @octdoc  m:component/build
         */
        public function build()
        /**/
        {
            foreach ($this->children as $child) {
                $child->build();
            }
        }
    }
}
