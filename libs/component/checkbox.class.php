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
     * checkbox component.
     *
     * @octdoc      c:component/checkbox
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class checkbox extends \org\octris\ncurses\component\listbox
    /**/
    {
        /**
         * Constructor.
         *
         * @octdoc  m:checkbox/__construct
         * @param   int             $items          Items of checkbox.
         * @param   int             $x              Optional x position of checkbox.
         * @param   int             $y              Optional y position of checkbox.
         */
        public function __construct(array $items, $x = 0, $y = 0)
        /**/
        {
            $this->height = count($items);
            $this->width  = array_reduce($items, function(&$width, $item) {
                $width = max($width, strlen($item['label']) + 6);

                return $width;
            }, 0);

            array_walk($items, function(&$item, $no) {
                $item['label'] = str_pad(
                    ' [' . ($item['selected'] ? 'X' : ' ') . '] ' . $item['label'], 
                    $this->width, 
                    ' ', 
                    STR_PAD_RIGHT
                );

                if (isset($item['action'])) {
                    $action = $item['action'];

                    $item['action'] = function() use ($action, $no) {
                        $this->toggle($no + 1);

                        $action();
                    };
                } else {
                    $item['action'] = function() use ($no) {
                        $this->toggle($no + 1);
                    };
                }
            });

            $this->items = $items;
            $this->cnt   = count($items);

            $this->x = $x;
            $this->y = $y;
        }

        /**
         * Toggle checkbox item.
         *
         * @octdoc  m:checkbox/toggle
         * @param   int                     $no             Number of item to toggle.
         */
        public function toggle($no)
        /**/
        {
            if ($no < 1 || $no > $this->cnt) return;

            $res      = $this->parent->getResource();
            $border   = (int)$this->parent->hasBorder();

            $selected = ($this->items[$no - 1]['selected'] = !$this->items[$no - 1]['selected']);

            if ($no == $this->selected) {
                ncurses_wattron($res, NCURSES_A_REVERSE);
            }

            ncurses_mvwaddstr(
                $res,
                $this->y + ($no - 1) + $border, 
                $this->x + 2 + $border, 
                ($selected ? 'X' : ' ')
            );

            if ($no == $this->selected) {            
                ncurses_wattroff($res, NCURSES_A_REVERSE);
            }

            $this->parent->refresh();
        }
    }
}
