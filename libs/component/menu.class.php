<?php

/*
 * This file is part of the 'org.octris.ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\ncurses\component {
    /**
     * Menu component.
     *
     * @octdoc      c:component/menu
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class menu extends \org\octris\ncurses\component\window
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
         * @param   int             $width          Optional width of window.
         * @param   int             $height         Optional height of window.
         * @param   int             $x              Optional x position of window.
         * @param   int             $y              Optional y position of window.
         */
        public function __construct(array $items, $height = 0, $x = 0, $y = 0)
        /**/
        {
            $this->listbox = new \org\octris\ncurses\component\listbox($items, $x + 1, $y + 1);
            $this->listbox->setParent($this);

            $size = $this->listbox->getSize();

            parent::__construct($size->width + 2, $size->height + 2, $x, $y);
        }

        /**
         * A menu cannot have child components.
         *
         * @octdoc  m:container/addChild
         * @param   \org\octris\ncurses\component       $child          Child component to add.
         */
        public function addChild(\org\octris\ncurses\component $child)
        /**/
        {
            throw new \Exception('A menu cannot have child components!');
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
         * Execute menu.
         *
         * @octdoc  m:menu/run
         */
        public function run()
        /**/
        {
            $this->listbox->run();
        }
    }
}
