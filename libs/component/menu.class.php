<?php

/*
 * This file is part of the 'org.octris.core' package.
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
         * Currently selected item.
         *
         * @octdoc  p:menu/$selected
         * @var     int
         */
        protected $selected = 1;
        /**/

        /**
         * Menu items.
         *
         * @octdoc  p:menu/$items
         * @var     array
         */
        protected $items = array();
        /**/

        /**
         * Number of menu items.
         *
         * @octdoc  p:menu/$cnt
         * @var     int
         */
        protected $cnt = 0;
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
            $height = ($height > 0 ? $height : count($items));
            $width  = array_reduce($items, function(&$width, $item) {
                $width = max($width, strlen($item['label']) + 2);

                return $width;
            }, 0);

            array_walk($items, function(&$item) use ($width) {
                $item['label'] = str_pad(' ' . $item['label'], $width, ' ', STR_PAD_RIGHT);
            });

            $this->items = $items;
            $this->cnt   = count($items);

            parent::__construct($width + 2, $height + 2, $x, $y);
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

            for ($i = 1; $i <= $this->cnt; ++$i) {
                if ($i == $this->selected) {
                    ncurses_wattron($this->resource, NCURSES_A_REVERSE);
                    ncurses_mvwaddstr($this->resource, $i, 1, $this->items[$i - 1]['label']);
                    ncurses_wattroff($this->resource, NCURSES_A_REVERSE);
                } else {
                    ncurses_mvwaddstr($this->resource, $i, 1, $this->items[$i - 1]['label']);
                }
            }
        }

        /**
         * Execute menu.
         *
         * @octdoc  m:menu/run
         */
        public function run()
        /**/
        {
            do {
                $pressed  = ncurses_getch($this->resource);
                $selected = $this->selected;

                if ($pressed == NCURSES_KEY_UP) {
                    $this->selected = max(1, $this->selected - 1);
                } elseif ($pressed == NCURSES_KEY_DOWN) {
                    $this->selected = min($this->cnt, $this->selected + 1);
                } elseif ($pressed == NCURSES_KEY_ESCAPE) {
                    break;
                }

                if ($selected != $this->selected) {
                    ncurses_wattron($this->resource, NCURSES_A_REVERSE);
                    ncurses_mvwaddstr($this->resource, $this->selected, 1, $this->items[$this->selected - 1]['label']);
                    ncurses_wattroff($this->resource, NCURSES_A_REVERSE);

                    ncurses_mvwaddstr($this->resource, $selected, 1, $this->items[$selected - 1]['label']);

                    $this->refresh();
                }
            } while(true);
        }
    }
}
