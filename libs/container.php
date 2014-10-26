<?php

/*
 * This file is part of the 'octris/ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\ncurses;

/**
 * Container super class.
 *
 * @octdoc      c:ncurses/container
 * @copyright   copyright (c) 2013-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
abstract class container
{
    use \octris\ncurses\event_tr;

    /**
     * Resource of container.
     *
     * @octdoc  p:container/$resource
     * @type    resource|null
     */
    protected $resource = null;
    /**/

    /**
     * Child widgets.
     *
     * @octdoc  p:container/$children
     * @type    array
     */
    protected $children = array();
    /**/

    /**
     * Stores instance of child that currently has the focus.
     *
     * @octdoc  p:container/$focused
     * @type    \octris\ncurses\widget
     */
    protected $focused = null;
    /**/

    /**
     * Whether container has a border.
     *
     * @octdoc  p:container/$has_border
     * @type    bool
     */
    protected $has_border = false;
    /**/

    /**
     * Parent container.
     *
     * @octdoc  p:container/$parent
     * @type    \octris\ncurses\container|null
     */
    protected $parent = null;
    /**/

    /**
     * Whether to exit main loop. This property is modified using the ~doExit~ method and
     * will be queried by the main loop to exit the container.
     *
     * @octdoc  p:container/$do_exit
     * @type    array|bool
     */
    protected $do_exit = false;
    /**/

    /**
     * Set parent container for widget.
     *
     * @octdoc  m:container/setParent
     * @param   \octris\ncurses\container      $parent         Parent container.
     */
    final public function setParent(\octris\ncurses\container $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get size of container.
     *
     * @octdoc  m:container/getMaxXY
     * @return  array                           Returns an array of two values x, y.
     */
    public function getMaxXY()
    {
        ncurses_wrefresh($this->resource);
        ncurses_getmaxyx($this->resource, $y, $x);

        return array($x, $y);
    }

    /**
     * Get size of container.
     *
     * @octdoc  m:container/getSize
     * @return  stdClass                                            Size ->width, ->height
     */
    public function getSize()
    {
        list($width, $height) = $this->getMaxXY();

        return (object)array(
            'width'  => $width,
            'height' => $height
        );
    }

    /**
     * Get inner size of container. The size returned by this method differs from the
     * size returned by ~getSize~ if the container has a border.
     *
     * @octdoc  m:container/getInnerSize
     * @return  stdClass                                            Size ->width, ->height
     */
    public function getInnerSize()
    {
        $size = $this->getSize();

        if ($this->has_border) {
            $size->width  -= 2;
            $size->height -= 2;
        }

        return $size;
    }

    /**
     * Get resource of container.
     *
     * @octdoc  m:container/getResource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set status for leaving container.
     *
     * @octdoc  m:container/doExit
     * @param   mixed                                                           $r_value            Optional value to return.
     */
    public function doExit($r_value = null)
    {
        $this->do_exit = array('r_value' => $r_value);
    }

    /**
     * Add child widget.
     *
     * @octdoc  m:container/addChild
     * @param   \octris\ncurses\widget|\octris\ncurses\container       $child          Child widget to add.
     * @return  \octris\ncurses\widget|\octris\ncurses\container                       The instance of the child widget.
     */
    public function addChild($child)
    {
        if (!($child instanceof \octris\ncurses\container ||
              $child instanceof \octris\ncurses\widget)) {
            throw new \Exception('"\octris\ncurses\container" or "\octris\ncurses\widget" expected');
        }

        $child->setParent($this);

        $this->children[] = $child;

        return $child;
    }

    /**
     * Setup container UI.
     *
     * @octdoc  a:container/setup
     */
    abstract protected function setup();
    /**/

    /**
     * Refresh container.
     *
     * @octdoc  m:container/refresh
     */
    public function refresh()
    {
        ncurses_wrefresh($this->resource);

        foreach ($this->children as $child) {
            if ($child instanceof \octris\ncurses\container) {
                $child->refresh();
            }
        }
    }

    /**
     * Set focus for a widget in container.
     *
     * @octdoc  m:container/focus
     * @param   \octris\ncurses\widget       $widget          The widget to focus.
     */
    public function focus(\octris\ncurses\widget $widget)
    {
        // remove focus from widget
        if (!is_null($this->focused)) {
            $this->focused->onBlur();
        }

        // set new focus
        $this->focused = $widget;
        $this->focused->onFocus();

        $this->refresh();
    }

    /**
     * Move focus to next focusable widget.
     *
     * @octdoc  m:container/moveFocus
     */
    public function moveFocus()
    {
        $next = is_null($this->focused);
        $idx  = 0;
        $cnt  = count($this->children);

        while (true && $cnt > 0) {
            $child = $this->children[($idx++ % $cnt)];

            if (!$child->isFocusable()) continue;

            if ($next) {
                $child->focus();
                break;
            } elseif ($idx > $cnt) {
                // nothing to focus
                break;
            } else {
                $next = ($child == $this->focused);
            }
        }
    }

    /**
     * Render widget.
     *
     * @octdoc  m:container/build
     */
    public function build()
    {
        $this->addKeyEvent(NCURSES_KEY_TAB, function () { $this->moveFocus(); });

        $this->setup();

        foreach ($this->children as $child) {
            $child->build();
        }
    }

    /**
     * Main loop.
     *
     * @octdoc  m:container/run
     */
    protected function run()
    {
        do {
            $key_code = ncurses_getch($this->resource);

            if (!($propagate = is_null($this->focused))) {
                $propagate = ($this->focused->propagateKeyEvent($key_code) && $this->do_exit === false);

                $this->focused->onKeypress(null);
            }

            if ($propagate) {
                $this->propagateKeyEvent($key_code);
            }
        } while ($this->do_exit === false);

        $return = $this->do_exit['r_value'];
        $this->do_exit = false;

        return $return;
    }
}

