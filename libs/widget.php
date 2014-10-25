<?php

/*
 * This file is part of the 'octris/ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\ncurses {
    /**
     * Ncurses widget class.
     *
     * @octdoc      c:ncurses/widget
     * @copyright   copyright (c) 2013-2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class widget
    {
        use \octris\ncurses\event_tr;

        /**
         * Resource of widget.
         *
         * @octdoc  p:widget/$resource
         * @type    resource|null
         */
        protected $resource = null;
        /**/

        /**
         * Parent container.
         *
         * @octdoc  p:widget/$parent
         * @type    \octris\ncurses\container|null
         */
        protected $parent = null;
        /**/

        /**
         * Whether widget can take the focus.
         *
         * @octdoc  p:widget/$focusable
         * @type    bool
         */
        protected $focusable = true;
        /**/

        /**
         * Stores registered event handlers.
         *
         * @octdoc  p:widget/$events
         * @type    array
         */
        protected $events = array();
        /**/

        /**
         * Stores event handler IDs.
         *
         * @octdoc  p:widget/$events_cnt
         * @type    array
         */
        protected $events_cnt = array();
        /**/

        /**
         * Set parent container for widget.
         *
         * @octdoc  m:widget/setParent
         * @param   \octris\ncurses\container       $parent         Parent container.
         */
        final public function setParent(\octris\ncurses\container $parent)
        {
            $this->parent = $parent;
        }

        /**
         * Get parent container of widget.
         *
         * @octdoc  m:widget/getParent
         * @return  \octris\ncurses\container                       Parent container.
         */
        public function getParent()
        {
            return $this->parent;
        }

        /**
         * Get resource of widget.
         *
         * @octdoc  m:widget/getResource
         */
        public function getResource()
        {
            return $this->resource;
        }

        /**
         * Add event handler.
         *
         * @octdoc  m:widget/addEvent
         * @param   string          $name               Name of event to add handler for.
         * @param   callable        $cb                 Callback to trigger for event.
         * @param   bool            $propagate          Whether to propagate event to other handlers.
         * @return  string                              ID the event handler is registered as.
         */
        public function addEvent($name, callable $cb, $propagate = true)
        {
            $name = strtolower($name);

            if (!isset($this->events_cnt[$name])) {
                $no = $this->events_cnt[$name] = 1;
            } else {
                $no = ++$this->events_cnt[$name];
            }

            $id = $name . '-' . $no;

            if (!isset($this->events[$name])) {
                $this->events[$name] = array();
            }

            array_unshift($this->events[$name], array(
                'id'        => $id,
                'callback'  => $cb,
                'propagate' => $propagate
            ));

            return $id;
        }

        /**
         * Remove event handler.
         *
         * @octdoc  m:widget/removeEvent
         * @param   string              $id                 ID of event handler to remove.
         */
        public function removeEvent($id)
        {
            $id   = strtolower($id);
            $name = explode('-', $id)[0];

            if (isset($this->events[$name])) {
                $this->events[$name] = array_filter(
                    $this->events[$name], 
                    function($item) use ($id) {
                        return ($item['id'] != $id);
                    }
                );
            }
        }

        /**
         * Propagate event.
         *
         * @octdoc  m:widget/propagateEvent
         * @param   string              $name               Name of event to propagate.
         * @param   array               $args               Optional arguments for event handler.
         */
        protected function propagateEvent($name, array $args = array())
        {
            $name = strtolower($name);

            if (!isset($this->events[$name])) return;

            foreach ($this->events[$name] as $handler) {
                call_user_func_array($handler['callback'], $args);

                if (!$handler['propagate']) break;
            }
        }

        /**
         * Return whether widget can take the focus.
         *
         * @octdoc  m:widget/isFocusable
         * @return  bool                                Whether widget can take the focus.
         */
        public function isFocusable()
        {
            return $this->focusable;
        }

        /**
         * Event is triggered if widget loses focus.
         *
         * @octdoc  m:widget/onBlur
         */
        public function onBlur()
        {
        }

        /**
         * Event is triggered if widget get's the focus.
         *
         * @octdoc  m:widget/onFocus
         */
        public function onFocus()
        {
        }

        /**
         * Event is triggered if widget is focused and a key is pressed.
         * Note however, that TAB keys will be handled by the container class.
         *
         * @octdoc  m:widget/onKeypress
         * @param   int                 $key            Code of the key that was pressed.
         */
        public function onKeypress($key_code)
        {
            $this->propagateEvent('keypress', array($key_code));
        }

        /**
         * Move focus to the widget.
         *
         * @octdoc  m:widget/focus
         */
        public function focus()
        {
            $this->parent->focus($this);
        }

        /**
         * Render widget.
         *
         * @octdoc  m:widget/build
         */
        public function build()
        {
            $this->addKeyEvent(NCURSES_KEY_CR,  function() { 
                $this->parent->moveFocus(); 
            }, true);
        }
    }
}
