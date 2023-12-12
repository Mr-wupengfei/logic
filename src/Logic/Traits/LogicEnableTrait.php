<?php

namespace Ykk\Logic\Traits;

use Ykk\Logic\Constants\Constants;

trait LogicEnableTrait
{
    public function setEnable()
    {
        if (empty($this->params)) {
            return;
        }
        if (isset($this->params['enable'])) {
            if (strtoupper($this->params['enable']) == Constants::T) {
                $this->params['disabled_at'] = null;
            } elseif (strtoupper($this->params['enable']) == Constants::F) {
                $this->params['disabled_at'] = now()->toDateTimeString();
            }
            unset($this->params['enable']);
        }
    }
}
