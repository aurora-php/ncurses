<?php

/*
 * This file is part of the 'octris/ncurses' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace octris\ncurses\widget;

/**
 * Textline widget.
 *
 * @octdoc      c:widget/textline
 * @copyright   copyright (c) 2013-2014 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class textline extends \octris\ncurses\widget
{
    /**
     * X position of textline.
     *
     * @octdoc  p:textline/$x
     * @type    int
     */
    protected $x;
    /**/

    /**
     * Y position of textline.
     *
     * @octdoc  p:textline/$y
     * @type    int
     */
    protected $y;
    /**/

    /**
     * Size of textline.
     *
     * @octdoc  p:textline/$size
     * @type    int
     */
    protected $size;
    /**/

    /**
     * Value of textline.
     *
     * @octdoc  p:textline/$value
     * @type    mixed
     */
    protected $value = '';
    /**/

    /**
     * Curser position on screen.
     *
     * @octdoc  p:textline/$cursor_x
     * @type    int
     */
    protected $cursor_x = 0;
    /**/

    /**
     * Offset of value to start display at.
     *
     * @octdoc  p:textline/$value_offset
     * @type    int
     */
    protected $value_offset = 0;
    /**/

    /**
     * Maximum length of input.
     *
     * @octdoc  p:textline/$max_length
     * @type    int|INF
     */
    protected $max_length;
    /**/

    /**
     * Constructor.
     *
     * @octdoc  m:textline/__construct
     * @param   int             $x              X position of textline.
     * @param   int             $y              Y position of textline.
     * @param   int             $size           Size of textline.
     * @param   mixed           $value          Optional value to show in textline.
     * @param   int|INF         $max_length     Maximum input length, default is PHP_INT_MAX
     */
    public function __construct($x, $y, $size, $value = '', $max_length = PHP_INT_MAX)
    {
        $this->x          = $x;
        $this->y          = $y;
        $this->size       = $size;
        $this->value      = substr($value, 0, $max_length);
        $this->max_length = $max_length;
    }

    /**
     * Set value for textline.
     *
     * @octdoc  m:listbox/setValue
     * @param   mixed           $value          Value to set.
     */
    public function setValue($value)
    {
        $this->value = substr($value, 0, $max_length);

        $this->cursor_x = $this->value_offset = 0;

        ncurses_mvwaddstr(
            $this->parent->getResource(),
            $this->y, $this->x,
            substr($this->value . str_repeat(' ', $this->size), 0, $this->size)
        );

        $this->parent->refresh();
    }

    /**
     * Get Focus.
     *
     * @octdoc  m:textline/onFocus
     */
    public function onFocus()
    {
        $res = $this->parent->getResource();

        ncurses_wmove($res, $this->y, $this->x + $this->cursor_x);
        ncurses_curs_set(1);

        $this->parent->refresh();
    }

    /**
     * Lose focus.
     *
     * @octdoc  m:textline/onBlur
     */
    public function onBlur()
    {
        ncurses_curs_set(0);
    }

    /**
     * Get's called when ENTER key is pressed in textline.
     *
     * @octdoc  m:textline/onAction
     */
    public function onAction()
    {
        $this->propagateEvent('action');
    }

    /**
     * Build textline.
     *
     * @octdoc  m:textline/build
     */
    public function build()
    {
        $res = $this->parent->getResource();

        $show_value = function () use ($res) {
            ncurses_mvwaddstr(
                $res, $this->y, $this->x,
                substr($this->value . str_repeat(' ', $this->size), $this->value_offset, $this->size)
            );
            ncurses_wmove($res, $this->y, $this->x + $this->cursor_x);

            $this->parent->refresh();
        };
        $move_cursor = function () use ($res) {
            ncurses_wmove($res, $this->y, $this->x + $this->cursor_x);

            $this->parent->refresh();
        };

        parent::build();

        ncurses_mvwaddstr($res, $this->y, $this->x, substr($this->value . str_repeat(' ', $this->size), 0, $this->size));

        // attach keyboard events
        $this->addKeyEvent(NCURSES_KEY_LEFT, function () use ($show_value, $move_cursor) {
            if (($this->cursor_x > 0 && ($this->value_offset == 0 || $this->size == 1)) || $this->cursor_x > 1) {
                --$this->cursor_x;

                $move_cursor();
            } elseif ($this->value_offset > 0) {
                $this->cursor_x      = min($this->value_offset, ceil($this->size / 2));
                $this->value_offset -= $this->cursor_x;

                $show_value();
            }
        });
        $this->addKeyEvent(NCURSES_KEY_RIGHT, function () use ($show_value, $move_cursor) {
            if ($this->cursor_x < min(strlen(rtrim($this->value)), $this->size - 1)) {
                ++$this->cursor_x;

                $move_cursor();
            } elseif ($this->value_offset + $this->size < strlen($this->value)) {
                ++$this->value_offset;

                $show_value();
            }
        });
        $this->addKeyEvent(NCURSES_KEY_BACK, function () use ($show_value, $move_cursor) {
            if ($this->cursor_x > 0 || $this->value_offset > 0) {
                if (($this->cursor_x > 0 && ($this->value_offset == 0 || $this->size == 1)) || $this->cursor_x > 1) {
                    --$this->cursor_x;
                } else {
                    $this->cursor_x      = min($this->value_offset, ceil($this->size / 2));
                    $this->value_offset -= $this->cursor_x;
                }

                $this->value = substr_replace($this->value, '', $this->value_offset + $this->cursor_x, 1);

                $show_value();
            } else {
                $move_cursor();
            }
        });
        $this->addKeyEvent(
            function ($key_code) {
                return ($key_code <= 255 && ctype_print($key_code));
            },
            function ($key_code) use ($show_value) {
                $this->value = substr(
                    substr_replace($this->value, chr($key_code), $this->value_offset + $this->cursor_x, 0),
                    0, $this->max_length
                );

                if ($this->cursor_x < $this->size - 1) {
                    ++$this->cursor_x;
                } elseif ($this->value_offset + $this->cursor_x < $this->max_length) {
                    ++$this->value_offset;
                }

                $show_value();
            }
        );
    }
}

