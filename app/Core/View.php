<?php

namespace App\Core;


use App\Lib\Helper;
use Phact\Helpers\Json;
use function is_array;

/**
 * Class View
 * @package App\Core
 */
class View
{
    /**
     * @return string
     */
    public function render(): string
    {
        $args = func_get_args();
        $countArgs = func_num_args();
        $countString = 0;

        for ($i = 0; $i < $countArgs; $i++) {
            if (is_string($args[$i]) || $args[$i] === null) {
                if ($countString === 0) {
                    $contentView = $args[$i];

                } elseif ($countString === 1) {
                    $layout = $args[$i];

                } elseif ($countString === 2) {
                    $data = $args[$i] === null ? null : ['var' => $args[$i]];
                }

                $countString++;
            }

            if (is_array($args[$i])) {
                $data = $args[$i];
            }
        }

        $attr = debug_backtrace(2, 3)[2];
        $methodName = $attr['function'];
        $methodName = Helper::upperCaseToHyphen($methodName);

        $controllerName = str_replace(['App\Controllers\\','Controller'], '', $attr['class']);
        $controllerName = Helper::upperCaseToHyphen($controllerName);

        $contentView = $contentView ?? $controllerName . '/' . $methodName;
        $layout = $layout ?? 'layouts/default';
        $data = $data ?? [];

        $_content_app = '';

        if (!empty($data) && is_array($data)) {
            extract($data, EXTR_OVERWRITE);
        }

        ob_start();
        include PROJECT_ROOT . '/app/views/' . $contentView . '.html.php';
        $_content_app = ob_get_clean();

        ob_start();
        include PROJECT_ROOT . '/app/views/' . $layout . '.html.php';
        $_content_app = ob_get_clean();

        return $_content_app;
    }

    /**
     * @param array|null $data
     * @return string
     */
    public function jsonEncode(?array $data): string
    {
        return Json::encode($data);
    }
}