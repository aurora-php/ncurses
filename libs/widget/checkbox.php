<?php

/*
 * This file is part of the 'octris/ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\ncurses\widget;

/**
 * checkbox widget.
 *
 * @octdoc      c:widget/checkbox
 * @copyright   copyright (c) 2013-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class checkbox extends \octris\ncurses\widget\listbox
{
    /**
     * Constructor.
     *
     * @octdoc  m:checkbox/__construct
     * @param   int             $x              X position of checkbox.
     * @param   int             $y              Y position of checkbox.
     * @param   int             $items          Items of checkbox.
     */
    public function __construct($x, $y, array $items)
    {
        // determine width and height of list
        $this->height = count($items);
        $this->width  = array_reduce($items, function ($width, $item) {
            $width = max($width, strlen($item['label']) + 6);

            return $width;
        }, 0);

        // add check button to label and add action for toggling check button
        array_walk($items, function (&$item, $no) {
            $item['label'] = str_pad(
                ' [' . ($item['selected'] ? 'X' : ' ') . '] ' . $item['label'], 
                $this->width, 
                ' ', 
                STR_PAD_RIGHT
            );

            $item['action'] = function () use ($item, $no) {
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
     * Toggle checkbox item.
     *
     * @octdoc  m:checkbox/toggle
     * @param   int                     $no             Number of item to toggle.
     */
    public function toggle($no)
    {
        if ($no < 1 || $no > $this->cnt) return;

        $res = $this->parent->getResource();

        $selected = ($this->items[$no - 1]['selected'] = !$this->items[$no - 1]['selected']);

        if ($no == $this->selected) {
            ncurses_wattron($res, NCURSES_A_REVERSE);
        }

        ncurses_mvwaddstr(
            $res,
            $this->y + ($no - 1), 
            $this->x + 2, 
            ($selected ? 'X' : ' ')
        );

        if ($no == $this->selected) {            
            ncurses_wattroff($res, NCURSES_A_REVERSE);
        }

        $this->items[$no - 1]['label'][2] = ($selected ? 'X' : ' ');

        $this->parent->refresh();
    }
}

