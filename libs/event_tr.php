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
     * Implements functionality for handling keyboard events.
     *
     * @octdoc      t:ncurses/event_tr
     * @copyright   copyright (c) 2013-2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    trait event_tr
    /**/
    {
        /**
         * Stores registered key event handlers.
         *
         * @octdoc  p:event_tr/$key_events
         * @type    array
         */
        protected $key_events = array();
        /**/

        /**
         * Stores key event handler ID.
         *
         * @octdoc  p:event_tr/$key_events_cnt
         * @type    int
         */
        protected $key_events_cnt = 1;
        /**/

        /**
         * Add key event.
         *
         * @octdoc  m:event_tr/addKeyEvent
         * @param   string|int|callable         $test               Either a character, a ASCII code of a character or a callback for validating character to set event handler for.
         * @param   callable                    $cb                 Callback to trigger for event.
         * @param   bool                        $propagate          Whether to propagate event to other handlders.
         * @return  string                                          ID the event handler is registered as.
         */
        public function addKeyEvent($test, callable $cb, $propagate = false)
        /**/
        {
            if (is_string($test)) {
                $test = ord($test);
                $test = function($key_code) use ($test) { 
                    return ($key_code === $test);
                };
            } elseif (is_int($test)) {
                $test = function($key_code) use ($test) {
                    return ($key_code === $test);
                };
            } elseif (!is_callable($test)) {
                throw new \Exception('char must be either a character, an integer or a callback function');
            }

            $id = $this->key_events_cnt++;

            array_unshift($this->key_events, array(
                'id'        => $id,
                'test'      => $test,
                'callback'  => $cb,
                'propagate' => $propagate
            ));

            return $id;
        }

        /**
         * Remove key event handler.
         *
         * @octdoc  m:event_tr/removeKeyEvent
         * @param   string              $id                 ID of event handler to remove.
         */
        public function removeKeyEvent($id)
        /**/
        {
            $this->key_events = array_filter(
                $this->key_events, 
                function($item) use ($id) {
                    return ($item['id'] != $id);
                }
            );
        }

        /**
         * Propagate event. The method returns true, if propagation was not stopped by an event handler called using
         * this method.
         *
         * @octdoc  m:event_tr/propagateKeyEvent
         * @param   string|int          $char               Either a character or a ASCII code of an event to propagate.
         * @return  bool                                    Propagation status.
         */
        public function propagateKeyEvent($char)
        /**/
        {
            $propagate = true;

            if (is_string($char)) {
                $char = ord($char);
            } elseif (!is_int($char)) {
                throw new \Exception('char must be either a character or an integer');
            }

            foreach ($this->key_events as $handler) {
                if ($handler['test']($char)) {
                    $handler['callback']($char);

                    if (!($propagate = $handler['propagate'])) break;
                }
            }

            return $propagate;
        }
    }
}
