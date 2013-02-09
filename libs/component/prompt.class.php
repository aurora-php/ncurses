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
     * Prompt widget for building command lines.
     *
     * @octdoc      c:component/prompt
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class prompt extends \org\octris\ncurses\component
    /**/
    {
        /**
         * X position to start input at.
         *
         * @octdoc  p:prompt/$x
         * @var     int
         */
        protected $x;
        /**/

        /**
         * Y position to start input at.
         *
         * @octdoc  p:prompt/$y
         * @var     int
         */
        protected $y;
        /**/

        /**
         * Prompt.
         * 
         * @octdoc  p:prompt/$prompt
         * @var     string
         */
        protected $prompt;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:prompt/__construct
         * @param   int                             $x              X position to start input at.
         * @param   int                             $y              Y position to start input at.
         * @param   string                          $prompt         Optional prompt to display.
         */
        public function __construct($x, $y, $prompt = '')
        /**/
        {
            $this->x      = $x;
            $this->y      = $y;
            $this->prompt = $prompt;
        }

        /**
         * Render prompt.
         *
         * @octdoc  m:prompt/render
         */
        public function build()
        /**/
        {
            ncurses_mvwaddstr(
                $this->parent->getResource(), 
                $this->y, $this->y, $prompt
            );
        }
    }
}
