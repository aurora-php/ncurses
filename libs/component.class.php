<?php

/*
 * This file is part of the 'org.octris.ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\ncurses {
    /**
     * Ncurses component class.
     *
     * @octdoc      c:ncurses/component
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class component
    /**/
    {
        use \org\octris\ncurses\event_tr;

        /**
         * Resource of component.
         *
         * @octdoc  p:component/$resource
         * @var     resource|null
         */
        protected $resource = null;
        /**/

        /**
         * Parent container.
         *
         * @octdoc  p:component/$parent
         * @var     \org\octris\core\ncurses\container|null
         */
        protected $parent = null;
        /**/

        /**
         * Whether component can take the focus.
         *
         * @octdoc  p:component/$focusable
         * @var     bool
         */
        protected $focusable = true;
        /**/

        /**
         * Stores registered event handlers.
         *
         * @octdoc  p:component/$events
         * @var     array
         */
        protected $events = array();
        /**/

        /**
         * Stores event handler IDs.
         *
         * @octdoc  p:component/$events_cnt
         * @var     array
         */
        protected $events_cnt = array();
        /**/

        /**
         * Set parent container for component.
         *
         * @octdoc  m:component/setParent
         * @param   \org\octris\core\ncurses\container      $parent         Parent container.
         */
        final public function setParent(\org\octris\ncurses\container $parent)
        /**/
        {
            $this->parent = $parent;
        }

        /**
         * Get resource of component.
         *
         * @octdoc  m:component/getResource
         */
        public function getResource()
        /**/
        {
            return $this->resource;
        }

        /**
         * Add event handler.
         *
         * @octdoc  m:component/addEvent
         * @param   string          $name               Name of event to add handler for.
         * @param   callable        $cb                 Callback to trigger for event.
         * @param   bool            $propagate          Whether to propagate event to other handlers.
         * @return  string                              ID the event handler is registered as.
         */
        public function addEvent($name, callable $cb, $propagate = true)
        /**/
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
         * @octdoc  m:component/removeEvent
         * @param   string              $id                 ID of event handler to remove.
         */
        public function removeEvent($id)
        /**/
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
         * @octdoc  m:component/propagateEvent
         * @param   string              $name               Name of event to propagate.
         * @param   array               $args               Optional arguments for event handler.
         */
        protected function propagateEvent($name, array $args = array())
        /**/
        {
            $name = strtolower($name);

            if (!isset($this->events[$name])) return;

            foreach ($this->events[$name] as $handler) {
                call_user_func_array($handler['callback'], $args);

                if (!$handler['propagate']) break;
            }
        }

        /**
         * Return whether component can take the focus.
         *
         * @octdoc  m:component/isFocusable
         * @return  bool                                Whether component can take the focus.
         */
        public function isFocusable()
        /**/
        {
            return $this->focusable;
        }

        /**
         * Event is triggered if component loses focus.
         *
         * @octdoc  m:component/onBlur
         */
        public function onBlur()
        /**/
        {
        }

        /**
         * Event is triggered if component get's the focus.
         *
         * @octdoc  m:component/onFocus
         */
        public function onFocus()
        /**/
        {
        }

        /**
         * Event is triggered if component is focused and a key is pressed.
         * Note however, that TAB keys will be handled by the container class.
         *
         * @octdoc  m:component/onKeypress
         * @param   int                 $key            Code of the key that was pressed.
         */
        public function onKeypress($key_code)
        /**/
        {
            $this->propagateEvent('keypress', array($key_code));
        }

        /**
         * Move focus to the component.
         *
         * @octdoc  m:component/focus
         */
        public function focus()
        /**/
        {
            $this->parent->focus($this);
        }

        /**
         * Render component.
         *
         * @octdoc  a:component/build
         */
        abstract public function build();
        /**/
    }
}
