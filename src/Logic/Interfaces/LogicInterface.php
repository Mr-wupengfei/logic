<?php

namespace Ykk\Logic\Interfaces;

interface LogicInterface {
    public function lists(); // 列表
    public function show($id); // 单条
    public function add(); // 新增
    public function update($id); // 更新
    public function delete($id); // 删除
}
