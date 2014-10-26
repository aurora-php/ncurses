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
 * Menu container.
 *
 * @octdoc      c:component/menu
 * @copyright   copyright (c) 2013-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class menu extends \octris\ncurses\container\window
{
    /**
     * Instance of listbox used to display menu.
     *
     * @octdoc  p:menu/$listbox
     * @type    \octris\ncurses\component\listbox
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
    public function __construct(array $items, $height = 0, $x = null, $y = null)
    {
        $this->listbox = parent::addChild(new \octris\ncurses\widget\listbox(0, 0, $items));

        $size = $this->listbox->getSize();

        parent::__construct($size->width + 2, $size->height + 2, $x, $y);
    }

    /**
     * A menu cannot have child components.
     *
     * @octdoc  m:menu/addChild
     */
    public function addChild($child)
    {
        throw new \Exception('A menu cannot have child components!');
    }

    /**
     * Nothing to setup.
     *
     * @octdoc  m:menu/setup
     */
    protected function setup()
    {
    }

    /**
     * Add key event.
     *
     * @octdoc  m:menu/addKeyEvent
     * @param   string|int|callable         $test               Either a character, a ASCII code of a character or a callback for validating character to set event handler for.
     * @param   callable                    $cb                 Callback to trigger for event.
     * @param   bool                        $propagate          Whether to propagate event to other handlders.
     * @return  string                                          ID the event handler is registered as.
     */
    public function addKeyEvent($test, callable $cb, $propagate = false)
    {
        return $this->listbox->addKeyEvent($test, $cb, $propagate);
    }

    /**
     * Remove key event handler.
     *
     * @octdoc  m:menu/removeKeyEvent
     * @param   string              $id                 ID of event handler to remove.
     */
    public function removeKeyEvent($id)
    {
        $this->listbox->remoteKeyEvent($id);
    }

    /**
     * Propagate event. The method returns true, if propagation was not stopped by an event handler called using
     * this method.
     *
     * @octdoc  m:menu/propagateKeyEvent
     * @param   string|int          $char               Either a character or a ASCII code of an event to propagate.
     * @return  bool                                    Propagation status.
     */
    public function propagateKeyEvent($char)
    {
        return $this->listbox->propagateKeyEvent($char);
    }

    /**
     * Focus listbox when menu is showed.
     *
     * @octdoc  m:menu/onShow
     */
    public function onShow()
    {
        $this->listbox->focus();
    }

    /**
     * Build menu.
     *
     * @octdoc  m:menu/build
     */
    public function build()
    {
        parent::build();
    }
}

