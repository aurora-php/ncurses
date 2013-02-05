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
     * Listbox component.
     *
     * @octdoc      c:component/listbox
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class listbox extends \org\octris\ncurses\component
    /**/
    {
        /**
         * Currently selected item.
         *
         * @octdoc  p:listbox/$selected
         * @var     int
         */
        protected $selected = 1;
        /**/

        /**
         * Menu items.
         *
         * @octdoc  p:listbox/$items
         * @var     array
         */
        protected $items = array();
        /**/

        /**
         * Number of menu items.
         *
         * @octdoc  p:listbox/$cnt
         * @var     int
         */
        protected $cnt = 0;
        /**/

        /**
         * Width of listbox calculated from list items.
         *
         * @octdoc  p:listbox/$width
         * @var     int
         */
        protected $width;
        /**/

        /**
         * Height of listbox calculated from list items.
         *
         * @octdoc  p:listbox/$height
         * @var     int
         */
        protected $height;
        /**/

        /**
         * X position of listbox.
         * 
         * @octdoc  p:listbox/$x
         * @var     int
         */
        protected $x;
        /**/

        /**
         * Y position of listbox.
         * 
         * @octdoc  p:listbox/$y
         * @var     int
         */
        protected $y;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:listbox/__construct
         * @param   int             $x              X position of listbox.
         * @param   int             $y              Y position of listbox.
         * @param   int             $items          Items of listbox.
         */
        public function __construct($x, $y, array $items)
        /**/
        {
            $this->height = count($items);
            $this->width  = array_reduce($items, function(&$width, $item) {
                $width = max($width, strlen($item['label']) + 2);

                return $width;
            }, 0);

            array_walk($items, function(&$item) {
                $item['label'] = str_pad(' ' . $item['label'], $this->width, ' ', STR_PAD_RIGHT);
            });

            $this->items = $items;
            $this->cnt   = count($items);

            $this->x = $x;
            $this->y = $y;
        }

        /**
         * Get size of listbox.
         *
         * @octdoc  m:listbox/getSize
         * @return  stdClass                                            Size ->width, ->height
         */
        public function getSize()
        /**/
        {
            return (object)array(
                'width'  => $this->width, 
                'height' => $this->height
            );
        }

        /**
         * Get Focus.
         *
         * @octdoc  m:listbox/onFocus
         */
        public function onFocus()
        /**/
        {
            $res = $this->parent->getResource();

            ncurses_wattron($res, NCURSES_A_REVERSE);
            ncurses_mvwaddstr(
                $res, 
                $this->y + ($this->selected - 1), 
                $this->x, 
                $this->items[$this->selected - 1]['label']
            );
            ncurses_wattroff($res, NCURSES_A_REVERSE);
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

            ncurses_mvwaddstr(
                $res, 
                $this->y + ($this->selected - 1), 
                $this->x, 
                $this->items[$this->selected - 1]['label']
            );
        }

        /**
         * Get's called when ENTER key is pressed on a button.
         *
         * @octdoc  m:listbox/onAction
         */
        public function onAction()
        /**/
        {
            $this->propagateEvent('action');
        }

        /**
         * Trigger action if ENTER key is pressed.
         *
         * @octdoc  m:listbox/onKeypress
         * @param   int                 $key            Code of the key that was pressed.
         */
        public function onKeypress($key_code)
        /**/
        {
            $res      = $this->parent->getResource();
            $selected = $this->selected;

            if ($key_code == NCURSES_KEY_UP) {
                $this->selected = max(1, $this->selected - 1);
            } elseif ($key_code == NCURSES_KEY_DOWN) {
                $this->selected = min($this->cnt, $this->selected + 1);
            } elseif ($key_code == NCURSES_KEY_CR || $key_code == NCURSES_KEY_SPACE) {
                if (isset($this->items[$this->selected - 1]['action'])) {
                    $this->items[$this->selected - 1]['action']();
                }                    
            }

            if ($selected != $this->selected) {
                ncurses_wattron($res, NCURSES_A_REVERSE);
                ncurses_mvwaddstr(
                    $res, 
                    $this->y + ($this->selected - 1), 
                    $this->x, 
                    $this->items[$this->selected - 1]['label']
                );
                ncurses_wattroff($res, NCURSES_A_REVERSE);

                ncurses_mvwaddstr(
                    $res, 
                    $this->y + ($selected - 1), 
                    $this->x, 
                    $this->items[$selected - 1]['label']
                );

                $this->parent->refresh();
            }
        }
        
        /**
         * Build listbox.
         *
         * @octdoc  m:listbox/build
         */
        public function build()
        /**/
        {
            $res = $this->parent->getResource();

            for ($i = 1; $i <= $this->cnt; ++$i) {
                ncurses_mvwaddstr(
                    $res, 
                    $this->y + ($i - 1), 
                    $this->x, 
                    $this->items[$i - 1]['label']
                );
            }
        }
    }
}
