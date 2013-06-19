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
     * menubar container.
     *
     * @octdoc      c:component/menubar
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class menubar extends \org\octris\ncurses\widget
    /**/
    {
        /**
         * Menu items.
         *
         * @octdoc  m:menubar/$items
         * @var     array
         */
        protected $items = array();
        /**/

        /**
         * Number of menu items.
         *
         * @octdoc  m:menubar/$cnt
         * @var     int
         */
        protected $cnt = 0;
        /**/

        /**
         * Selected menubar item.
         *
         * @octdoc  m:menubar/$selected
         * @var     int
         */
        protected $selected = 0;
        /**/

        /**
         * Add a menu item to the menubar.
         *
         * @octdoc  m:menubar/addMenu
         * @param   string                  $label                      Label of menu to add.
         */
        public function addMenu($label)
        /**/
        {
            $this->items[] = array(
                'label' => $label,
                'x'     => 0
            );
        }

        /**
         * A menubar cannot have child components.
         *
         * @octdoc  m:menubar/addChild
         */
        public function addChild($child)
        /**/
        {
        }

        /**
         * Get Focus.
         *
         * @octdoc  m:menubar/onFocus
         */
        public function onFocus()
        /**/
        {
            $res = $this->parent->getResource();

            ncurses_mvwaddstr($res, 0, $this->items[$this->selected]['x'], $this->items[$this->selected]['label']);
        }

        /**
         * Lose focus.
         *
         * @octdoc  m:listbox/onBlur
         */
        public function onBlur()
        /**/
        {
            $res = $this->parent->getResource();

            ncurses_wattron($res, NCURSES_A_REVERSE);
            ncurses_mvwaddstr($res, 0, $this->items[$this->selected]['x'], $this->items[$this->selected]['label']);
            ncurses_wattroff($res, NCURSES_A_REVERSE);
        }

        /**
         * Open specified menu item.
         *
         * @octdoc  m:listbox/openMenu
         * @param   int                     $item                       Number of item to set.
         */
        public function openMenu($item)
        /**/
        {
            if ($item != $this->selected) {
                $res = $this->parent->getResource();

                ncurses_wattron($res, NCURSES_A_REVERSE);
                ncurses_mvwaddstr($res, 0, $this->items[$this->selected]['x'], $this->items[$this->selected]['label']);
                ncurses_wattroff($res, NCURSES_A_REVERSE);

                ncurses_mvwaddstr($res, 0, $this->items[$item]['x'], $this->items[$item]['label']);

                $this->parent->refresh();

                $this->selected = $item;
            }
        }

        /**
         * Build menubar.
         *
         * @octdoc  m:menubar/build
         */
        public function build()
        /**/
        {
            $res = $this->parent->getResource();

            ncurses_wattron($res, NCURSES_A_REVERSE);
            ncurses_wmove($res, 0, 0);
            ncurses_whline($res, 32, $this->parent->getSize()->width);
            ncurses_wattroff($res, NCURSES_A_REVERSE);

            $x = 0;

            foreach ($this->items as &$item) {
                $item['label'] = ' ' . trim($item['label']) . ' ';
                $item['x']     = $x;

                ncurses_wattron($res, NCURSES_A_REVERSE);
                ncurses_mvwaddstr($res, 0, $x, $item['label']);
                ncurses_wattroff($res, NCURSES_A_REVERSE);

                $x += strlen($item['label']);
                ++$this->cnt;
            }

            // attach keyboard events
            $this->addKeyEvent(NCURSES_KEY_LEFT, function() {
                $this->openMenu(($this->selected + 1) % $this->cnt); 
            });
            $this->addKeyEvent(NCURSES_KEY_RIGHT, function() {
                $this->openMenu(($this->cnt + ($this->selected - 1)) % $this->cnt);
            });
        }
    }
}
