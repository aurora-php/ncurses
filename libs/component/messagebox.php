<?php

/*
 * This file is part of the 'octris/ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\ncurses\component {
    /**
     * Messagebox component.
     *
     * @octdoc      c:container/messagebox
     * @copyright   copyright (c) 2013-2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class messagebox extends \octris\ncurses\container\window
    /**/
    {
        /**
         * Messagebox types.
         *
         * @octdoc  d:messagebox/...
         */
        const OK                = 1;
        const OKCANCEL          = 2;
        const RETRYCANCEL       = 3;
        const YESNO             = 4;
        const YESNOCANCEL       = 5;
        const ABORTRETRYIGNORE  = 6;
        const HELP              = 128;
        /**/

        /**
         * Messagebox action.
         *
         * @octdoc  d:messabox/ACTION_...
         */
        const ACTION_OK     = 'Ok';
        const ACTION_CANCEL = 'Cancel';
        const ACTION_RETRY  = 'Retry';
        const ACTION_ABORT  = 'Abort';
        const ACTION_IGNORE = 'Ignore';
        const ACTION_YES    = 'Yes';
        const ACTION_NO     = 'No';
        const ACTION_HELP   = 'Help';
        /**/ 

        /**
         * Messagebox types.
         *
         * @octdoc  p:messagebox/$types
         * @type    array
         */
        protected static $types = array(
            self::OK               => array(self::ACTION_OK),
            self::OKCANCEL         => array(self::ACTION_OK, self::ACTION_CANCEL),
            self::RETRYCANCEL      => array(self::ACTION_RETRY, self::ACTION_CANCEL),
            self::YESNO            => array(self::ACTION_YES, self::ACTION_NO),
            self::YESNOCANCEL      => array(self::ACTION_YES, self::ACTION_NO, self::ACTION_CANCEL),
            self::ABORTRETRYIGNORE => array(self::ACTION_ABORT, self::ACTION_RETRY, self::ACTION_IGNORE)
        );
        /**/

        /**
         * Type of messagebox.
         *
         * @octdoc  p:messagebox/$type
         * @type    int
         */
        protected $type;
        /**/

        /**
         * Text to display in messagebox.
         *
         * @octdoc  p:messagebox/$text
         * @type    string
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
                new \octris\ncurses\widget\text(
                    $this->text, 
                    \octris\ncurses\widget\text::ALIGN_CENTER,
                    1
                )
            );

            $size = $this->getInnerSize();
            $y    = $size->height - 2;

            $help    = ($this->type & self::HELP) == self::HELP;
            $buttons = self::$types[($this->type & ~self::HELP)];

            if ($help) {
                $buttons[] = self::ACTION_HELP;
            }

            $b_width = array_reduce($buttons, function($result, $button) {
                return $result + strlen($button) + 2;
            }, (count($buttons) - 1) * 2);

            $x = floor(($size->width - $b_width) / 2);

            foreach ($buttons as $button) {
                parent::addChild(
                    new \octris\ncurses\widget\button(
                        $x, $y, $button
                    )
                )->addEvent('action', function() use ($button) {
                    $this->doExit($button);
                });

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
