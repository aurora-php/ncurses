<?php

/*
 * This file is part of the 'org.octris.ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\ncurses\container {
    /**
     * Messagebox container.
     *
     * @octdoc      c:container/messagebox
     * @copyright   copyright (c) 2013 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class messagebox extends \org\octris\ncurses\container\window
    /**/
    {
        /**
         * Messagebox types.
         *
         * @octdoc  d:messagebox/T_...
         */
        const T_OK                = 1;
        const T_OKCANCEL          = 2;
        const T_RETRYCANCEL       = 3;
        const T_YESNO             = 4;
        const T_YESNOCANCEL       = 5;
        const T_ABORTRETRYIGNORE  = 6;
        const T_HELP              = 128;
        /**/

        /**
         * Messagebox action.
         *
         * @octdoc  d:messabox/T_ACTION_...
         */
        const T_ACTION_OK     = 'Ok';
        const T_ACTION_CANCEL = 'Cancel';
        const T_ACTION_RETRY  = 'Retry';
        const T_ACTION_ABORT  = 'Abort';
        const T_ACTION_IGNORE = 'Ignore';
        const T_ACTION_YES    = 'Yes';
        const T_ACTION_NO     = 'No';
        const T_ACTION_HELP   = 'Help';
        /**/ 

        /**
         * Messagebox types.
         *
         * @octdoc  p:messagebox/$types
         * @var     array
         */
        protected static $types = array(
            self::T_OK               => array(self::T_ACTION_OK),
            self::T_OKCANCEL         => array(self::T_ACTION_OK, self::T_ACTION_CANCEL),
            self::T_RETRYCANCEL      => array(self::T_ACTION_RETRY, self::T_ACTION_CANCEL),
            self::T_YESNO            => array(self::T_ACTION_YES, self::T_ACTION_NO),
            self::T_YESNOCANCEL      => array(self::T_ACTION_YES, self::T_ACTION_NO, self::T_ACTION_CANCEL),
            self::T_ABORTRETRYIGNORE => array(self::T_ACTION_ABORT, self::T_ACTION_RETRY, self::T_ACTION_IGNORE)
        );
        /**/

        /**
         * Type of messagebox.
         *
         * @octdoc  p:messagebox/$type
         * @var     int
         */
        protected $type;
        /**/

        /**
         * Text to display in messagebox.
         *
         * @octdoc  p:messagebox/$text
         * @var     string
         */
        protected $text;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:messagebox/__construct
         * @param   int             $type           Type of messagebox.
         * @param   string          $text           Text to display in window.
         * @param   int             $width          Optional width of window.
         * @param   int             $height         Optional height of window.
         * @param   int             $x              Optional x position of window.
         * @param   int             $y              Optional y position of window.
         */
        public function __construct($type, $text, $width = 0, $height = 0, $x = 0, $y = 0)
        /**/
        {
            parent::__construct($width, $height, $x, $y);

            $this->type = $type;
            $this->text = $text;
        }

        /**
         * Setup messagebox.
         *
         * @octdoc  m:messagebox/setup
         */
        protected function setup()
        /**/
        {
            parent::addChild(
                new \org\octris\ncurses\component\text(
                    $this->text, 
                    \org\octris\ncurses\component\text::T_ALIGN_CENTER,
                    1
                )
            );

            $size = $this->getInnerSize();
            $y    = $size->height - 2;

            $help    = ($this->type & self::T_HELP) == self::T_HELP;
            $buttons = self::$types[($this->type & ~self::T_HELP)];

            if ($help) {
                $buttons[] = self::T_ACTION_HELP;
            }

            $b_width = array_reduce($buttons, function($result, $button) {
                return $result + strlen($button) + 2;
            }, (count($buttons) - 1) * 2);

            $x = floor(($size->width - $b_width) / 2);

            foreach ($buttons as $button) {
                parent::addChild(
                    new \org\octris\ncurses\component\button(
                        $x, $y, $button
                    )
                );

                $x += strlen($button) + 4;
            }
        }

        /**
         * A messagebox cannot have child components.
         *
         * @octdoc  m:container/addChild
         */
        public function addChild($child)
        /**/
        {
            throw new \Exception('A messagebox cannot have child components!');
        }
    }
}
