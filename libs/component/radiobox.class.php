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
     * Radiobox component.
     *
     * @octdoc      c:component/radiobox
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class radiobox extends \org\octris\ncurses\component\listbox
    /**/
    {
        /**
         * 

        /**
         * Constructor.
         *
         * @octdoc  m:radiobox/__construct
         * @param   int             $items          Items of radiobox.
         * @param   int             $x              Optional x position of radiobox.
         * @param   int             $y              Optional y position of radiobox.
         */
        public function __construct(array $items, $x = 0, $y = 0)
        /**/
        {
            // determine width and height of list
            $this->height = count($items);
            $this->width  = array_reduce($items, function(&$width, $item) {
                $width = max($width, strlen($item['label']) + 6);

                return $width;
            }, 0);

            // make sure, that only one item (last one found) is selected
            $selected = false;
            for ($i = count($items) - 1; $i >= 0; --$i) {
                if ($items[$i]['selected']) {
                    $items[$i]['selected'] = ($selected ? false : ($selected = true));
                }
            }

            // add radio button to label and add action for toggling radio button
            array_walk($items, function(&$item, $no) {
                $item['label'] = str_pad(
                    ' (' . ($item['selected'] ? '*' : ' ') . ') ' . $item['label'], 
                    $this->width, 
                    ' ', 
                    STR_PAD_RIGHT
                );

                $item['action'] = function() use ($item, $no) {
                    // turn all buttons off
                    array_walk($this->items, function($item, $no) {
                        if ($item['selected']) $this->toggle($no + 1);
                    });

                    // press new button
                    $this->toggle($no + 1);

                    if (isset($item['action']) && is_callable($item['action'])) {
                        $item['action']();
                    }
                };
            });

            // misc
            $this->items = $items;
            $this->cnt   = count($items);

            $this->x = $x;
            $this->y = $y;
        }

        /**
         * Toggle radioboxbox item.
         *
         * @octdoc  m:radiobox/toggle
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
                ($selected ? '*' : ' ')
            );

            if ($no == $this->selected) {            
                ncurses_wattroff($res, NCURSES_A_REVERSE);
            }

            $this->items[$no - 1]['label'][2] = ($selected ? '*' : ' ');

            $this->parent->refresh();
        }
    }
}
