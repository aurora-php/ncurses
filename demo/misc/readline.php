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
function rl_callback($ret) {
}

readline_callback_handler_install('', 'rl_callback');

while (true) {
    $w = NULL;
    $e = NULL;
    $r = array(STDIN);
    $n = stream_select($r, $w, $e, null);

    if ($n && in_array(STDIN, $r)) {
        readline_callback_read_char();

        $info = readline_info();

        ncurses_mvaddstr(0, 0, $info['line_buffer']);
        ncurses_refresh();
    }
}
