<?php

namespace Ykk\Logic\Bases;

use Illuminate\Support\Arr;
use Ykk\Logic\Constants\ErrorCode;
use Ykk\Logic\Constants\HttpStatus;

class BaseLogic
{
    /**
     * 允许修改的类属性
     * @var string[]
     */
    private $allowProperties = [
        'page',
        'perPage',
    ];
    /**
     * 默认页码
     * @var int
     */
    public $page = 1;

    /**
     * 分页大小
     * @var int
     */
    public $perPage;

    /**
     * 扩展字段
     * @var array
     */
    public $extFields = [];


    /**
     * 验证实例
     * @var null
     */
    public $validate = null;

    /**
     * 参数数组
     * @var array
     */
    public $params = [];

    /**
     * 操作状态,成功还是失败
     * @var bool
     */
    public $success = false;

    /**
     * 消息内容
     * @var string
     */
    public $message = '';

    /**
     * 返回的数据
     * @var array
     */
    public $data = [];

    /**
     * 错误状态码,0,正常,非0错误
     * @var int
     */
    public $code = 0;

    /**
     * http状态码
     * @var int
     */
    public $status = 200;

    /**
     * http cookies
     * @var null|array
     */
    public $cookies = null;

    public $model = null;//使用的model

    /**
     * 设置验证实例
     * @param BaseRequest $validate
     */
    public function setValidate(BaseRequest $validate)
    {
        $this->validate = $validate;
        //参数
        $params = $this->validate->validated();

        //可修改的参数
        $allowFields = $this->validate->getAllowUpdateFields();

        $paramsKeys = array_keys($params);

        //要处理的参数
        $processParams = [];

        if (!empty($allowFields) && !empty($paramsKeys)) {
            //交集
            $paramsIntersect = array_intersect($paramsKeys, $allowFields);

            //获取交集的参数
            $processParams = Arr::only($params, $paramsIntersect);
        } else {

            //没有定义,获取所有参数
            $processParams = $params;
        }

        $this->setParams($processParams);
    }

    /**
     * 设置参数
     * @param $params
     */
    public function setParams($params)
    {
        //如果存在成员变量,则赋值
        foreach ($params as $k => $v) {
            if (in_array($k, $this->allowProperties, true)) {
                $this->$k = $v;
            }
            $this->params[$k] = $v;
        }
    }

    public function getValidate()
    {
        return $this->validate;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * 获取单个参数
     * @param $key
     * @param null $default
     * @return array|\ArrayAccess|mixed
     */
    public function getParam($key, $default = null)
    {
        return Arr::get($this->params, $key, $default);
    }

    public function getSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess($value)
    {
        $this->success = $value;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage($value)
    {
        $this->message = $value;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData($value)
    {
        $this->data = $value;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode($value)
    {
        $this->code = $value;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus($value)
    {
        $this->status = $value;
    }

    /**
     * 设置返回值参数
     * @param bool $success
     * @param string $message
     * @param array $data
     * @param int $code
     * @param int $status
     * @return bool
     */
    public function setReturn(bool $success = true, string $message = '操作成功', array $data = [],
                              int  $code = ErrorCode::CODE_10000,
                              int  $status = HttpStatus::CODE_200): bool
    {
        $this->setSuccess($success);
        $this->setMessage($message);
        $this->setData($data);

        if (!$success && ((int) $code === ErrorCode::CODE_10000)) {
            //操作失败,还未指定错误码,
            $code = ErrorCode::CODE_40000;
        }

        $this->setCode($code);
        $this->setStatus($status);

        return $success;
    }


    /**
     * 返回数据结构
     * @return array
     */
    public function getReturnData(): array
    {
        return [
            'success' => (bool) $this->success,
            'message' => $this->message,
            'code' => $this->code,
            'data' => $this->data,
        ];
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage ?: config('ykk_logic.pagination.per_page', 15);
    }

    /**
     * @param int $per_page
     */
    public function setPerPage(int $per_page)
    {
        $this->perPage = $per_page;
    }

    /**
     * 获取分页参数
     * @return int|mixed
     */
    public function getParamPerPage()
    {
        $this->perPage = !empty($this->params['per_page'])?$this->params['per_page']:$this->perPage;

        return $this->perPage;
    }

    /**
     * 获取排序字段
     *
     * @param string $default 默认排序字段
     *
     * @return string
     */
    public function getParamOrderBy(string $default = 'id'): string
    {
        return $this->params['order_by'] ?? $default;
    }

    /**
     * 获取排序规则
     *
     * @param string $default 默认排序规则
     *
     * @return string
     */
    public function getParamOrderRule(string $default = 'desc'): string
    {
        return $this->params['order_rule'] ?? $default;
    }

    public function setCookies($cookies)
    {
        $this->cookies = $cookies;
    }
}
