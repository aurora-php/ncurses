<?php

/*
 * This file is part of the 'octris/ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\ncurses\component;

/**
 * menubar container.
 *
 * @octdoc      c:component/menubar
 * @copyright   copyright (c) 2013-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class menubar extends \octris\ncurses\widget
{
    /**
     * Menu items.
     *
     * @octdoc  m:menubar/$items
     * @type    array
     */
    protected $items = array();
    /**/

    /**
     * Number of menu items.
     *
     * @octdoc  m:menubar/$cnt
     * @type    int
     */
    protected $cnt = 0;
    /**/

    /**
     * Selected menubar item.
     *
     * @octdoc  m:menubar/$selected
     * @type    int|null
     */
    protected $selected = null;
    /**/

    /**
     * Add a menu item to the menubar.
     *
     * @octdoc  m:menubar/addMenu
     * @param   string                                  $label                      Label of menu to show in menubar.
     * @param   \octris\ncurses\component\menu      $menu                       Menu object.
     */
    public function addMenu($label, \octris\ncurses\component\menu $menu)
    {
        $this->items[] = array(
            'label' => $label,
            'menu'  => $menu,
            'x'     => 0
        );

        ++$this->cnt;
    }

    /**
     * A menubar cannot have child components.
     *
     * @octdoc  m:menubar/addChild
     */
    public function addChild($child)
    {
    }

    /**
     * Get Focus.
     *
     * @octdoc  m:menubar/onFocus
     */
    public function onFocus()
    {
        $res = $this->parent->getResource();

        $this->openMenu($this->selected);
    }

    /**
     * Lose focus.
     *
     * @octdoc  m:listbox/onBlur
     */
    public function onBlur()
    {
        $res = $this->parent->getResource();

        $this->closeMenu();
    }

    /**
     * Close current open menu.
     *
     * @octdoc  m:listbox/closeMenu
     */
    protected function closeMenu()
    {
        if (!is_null($this->selected)) {
            $res = $this->parent->getResource();

            ncurses_wattron($res, NCURSES_A_REVERSE);
            ncurses_mvwaddstr($res, 0, $this->items[$this->selected]['x'], $this->items[$this->selected]['label']);
            ncurses_wattroff($res, NCURSES_A_REVERSE);
    
            $this->items[$this->selected]['menu']->hide();
        }
    }

    /**
     * Open specified menu item.
     *
     * @octdoc  m:listbox/openMenu
     * @param   int                     $item                       Number of item to set.
     */
    public function openMenu($item)
    {
        if (is_null($item)) $item = 0;

        if ($item !== $this->selected) {
            $res = $this->parent->getResource();

            $this->closeMenu();

            ncurses_mvwaddstr($res, 0, $this->items[$item]['x'], $this->items[$item]['label']);

            $this->parent->refresh();

            $this->selected = $item;

            $this->items[$item]['menu']->show();
        }
    }

    /**
     * Build menubar.
     *
     * @octdoc  m:menubar/build
     */
    public function build()
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

            $item['menu']->moveTo($x, 1);
            $item['menu']->addKeyEvent(NCURSES_KEY_LEFT, function () {
                $this->openMenu(($this->selected + 1) % $this->cnt); 
            });
            $item['menu']->addKeyEvent(NCURSES_KEY_RIGHT, function () {
                $this->openMenu(($this->cnt + ($this->selected - 1)) % $this->cnt);
            });
            $item['menu']->addKeyEvent(NCURSES_KEY_CR, function () {
                $this->closeMenu();
            }, true);
            $item['menu']->addKeyEvent(NCURSES_KEY_SPACE, function () {
                $this->closeMenu();
            }, true);

            ncurses_wattron($res, NCURSES_A_REVERSE);
            ncurses_mvwaddstr($res, 0, $x, $item['label']);
            ncurses_wattroff($res, NCURSES_A_REVERSE);

            $x += strlen($item['label']);
        }
    }
}

