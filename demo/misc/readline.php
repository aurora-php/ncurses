#!/usr/bin/env php
<?php

// initialize ncurses
ncurses_init();
ncurses_noecho();

register_shutdown_function(function() {
    ncurses_end();
});

ncurses_newwin(0, 0, 0, 0);

// readline callback handler
function rl_callback($line) {
    $line = rtrim($line);

    if ($line != '') readline_add_history($line);
}

function rl_info() {
    $info = readline_info();
    $info['history_items'] = count(readline_list_history());

    $row = 2;
    array_walk($info, function(&$v, $k) use (&$row) {
        ncurses_mvaddstr($row++, 0, sprintf("%-20s = %s", $k, $v));
        ncurses_clrtoeol();
    });

    ncurses_move(2, 23 + $info['point']);
    ncurses_refresh();
}

readline_callback_handler_install('', 'rl_callback');

ncurses_mvaddstr(0, 0, "press any key (CTRL+C to exit)");
ncurses_refresh();

rl_info();

while (true) {
    $w = NULL;
    $e = NULL;
    $r = array(STDIN);
    $n = stream_select($r, $w, $e, null);

    if ($n && in_array(STDIN, $r)) {
        readline_callback_read_char();

        rl_info();
    }
}
