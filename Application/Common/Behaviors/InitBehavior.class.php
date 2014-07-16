<?php

namespace Common\Behaviors;

class InitBehavior extends \Think\Behavior {

    public function run(&$param) {
        ini_set("session.save_handler", "redis");
        ini_set("session.save_path", "tcp://127.0.0.1:6379");
    }

}
