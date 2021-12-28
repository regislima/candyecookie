<?php

namespace Framework\Control;

use Framework\Widgets\Base\Element;

class Page extends Element {

    public function show() {
        if ($_GET) {
            $class = isset($_GET['class']) ? $_GET['class'] : null;
            $method = isset($_GET['method']) ? $_GET['method'] : null;

            if ($class) {
                $object = $class == get_class($this) ? $this : new $class;

                if (method_exists($object, $method)) {
                    call_user_func(array($object, $method), $_GET);
                }
            }
        }

        parent::show();
    }
}