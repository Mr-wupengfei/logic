# 基于laravel的逻辑层支持
## 功能汇总
- request参数根据rule自动校验，赋值logic成员属性
- 支持request校验失败后的 failed callback

## 包引入
    ```
    "repositories": {
        "laravel-logic": 
        {
            "type": "vcs",
            "url": "git@github.com:Mr-wupengfei/logic.git",
            "package": "ykk/laravel-logic"
        }
    }    
    ```
- 安装包
    ```
    composer install ykk/laravel-logic
    ```
- 配置文件
    ```
    php artisan vendor:publish --provider="Ykk\Logic\Providers\LogicServiceProvider"
    ```
## 使用方式
- 业务项目自身logic层继承该包的BaseLogic。
- 业务项目request继承包内的BaseRequest。
- request支持 matchFieldRuleHandle 和 matchruleRuleHandle 的callback，按照命名规则设置方法名，自动回调。
    ```
    // 字段名+失败规则
    protected function matchUsernameRequiredHandle()
    {
        echo '我报错了';
    }
    // 所有该失败规则都会走这里
    protected function matchRequiredHandle()
    {
        echo '112233';
    }
    ```
- request验证中使用isEdit校验是否编辑场景，需要controller的编辑方法包含edit即可触发场景校验。（暂时不用）
- 同一排序如何用
    ```
    // 如果用排序必须要声明
    protected $orderByFields = [
        'id',
        'name',
    ];
    
    protected function extendRules(): array
    {
        return array_merge($this->getOrderByRule(), [
            'username' => 'required',
        ]);
    }
  ```
