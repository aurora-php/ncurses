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
     * Menu container.
     *
     * @octdoc      c:container/menu
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class menu extends \org\octris\ncurses\container\window
    /**/
    {
        /**
         * Instance of listbox used to display menu.
         *
         * @octdoc  p:menu/$listbox
         * @var     \org\octris\ncurses\component\listbox
         */
        protected $listbox;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:menu/__construct
         * @param   int             $items          Items to display in menu.
         * @param   int             $height         Optional height of window.
         * @param   int             $x              Optional x position of window.
         * @param   int             $y              Optional y position of window.
         */
        public function __construct(array $items, $height = 0, $x = 0, $y = 0)
        /**/
        {
            $this->listbox = new \org\octris\ncurses\component\listbox($items, $x, $y);
            $this->listbox->setParent($this);

            $size = $this->listbox->getSize();

            parent::__construct($size->width + 2, $size->height + 2, $x, $y);
        }

        /**
         * A menu cannot have child components.
         *
         * @octdoc  m:container/addChild
         */
        public function addChild($child)
        /**/
        {
            throw new \Exception('A menu cannot have child components!');
        }

        /**
         * Nothing to setup.
         *
         * @octdoc  m:menu/setup
         */
        protected function setup()
        /**/
        {
        }

        /**
         * Focus listbox when menu is showed.
         *
         * @octdoc  m:menu/onShow
         */
        public function onShow()
        /**/
        {
            $this->listbox->focus();
        }

        /**
         * Build menu.
         *
         * @octdoc  m:menu/build
         */
        public function build()
        /**/
        {
            parent::build();

            $this->listbox->build();
        }

        /**
         * Run menu.
         *
         * @octdoc  m:menu/run
         */
        protected function run()
        /**/
        {
            $this->listbox->run();
        }
    }
}
