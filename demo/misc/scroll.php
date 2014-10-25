#!/usr/bin/env php
<?php

// initialize ncurses
ncurses_init();
// ncurses_noecho();

register_shutdown_function (function () {
    ncurses_end();
});

ncurses_newwin(0, 0, 0, 0);

$height = 5;
$width  = 7;

$win = ncurses_newwin($height, $width, 10, 10);
ncurses_scrollok($win, true);

for ($y = 0; $y < 20; ++$y) {
    $text = sprintf("Row #%d\n", $y + 1);

    ncurses_mvwaddstr($win, ($y < $height ? $y : $height - 1), 0, substr($text, 0, $width));
    ncurses_wrefresh($win);
    sleep(1);
}

ncurses_delwin($win);
