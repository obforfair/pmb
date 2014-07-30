<?php

namespace Api\Behaviors;



class AppBeginBehavior extends \Think\Behavior {

    public function run(&$param) {
        register_shutdown_function('Lib\Ob\Ob::fatalError');
        set_error_handler('Lib\Ob\Ob::appError');
        set_exception_handler('Lib\Ob\Ob::appException');
    }

}
