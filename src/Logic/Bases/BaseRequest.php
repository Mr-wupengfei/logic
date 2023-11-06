<?php

namespace Ykk\Logic\Bases;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Ykk\Logic\Exceptions\ValidationException;

class BaseRequest extends FormRequest
{
    //允许修改的字段
    protected $allow_update_fields = [];

    const ACTION_EDIT_NAME = 'EDIT';

    protected $pageRule = [
        'page' => 'sometimes|integer|min:1',
        'per_page' => 'sometimes|integer|min:1',
    ];

    // 排序字段
    protected $orderByFields = [];

    // 获取排序规则
    public function getOrderByRule(): array
    {
        return [
            'order_by' => [
                'sometimes',
                Rule::in($this->orderByFields),
            ],
            'order_rule' => [
                'sometimes',
                Rule::in([
                    'asc',
                    'desc',
                ]),
            ],
        ];
    }

    //是否编辑场景，验证用
    public function isEdit(): bool
    {
        $action_name = $this->route()->getActionMethod();
        if (!empty($action_name)) {
            return Str::contains(strtoupper($action_name), self::ACTION_EDIT_NAME);
        }

        return false;
    }

    final public function setAllowUpdateFields($fields = [])
    {
        $this->allow_update_fields = $fields;
    }

    final public function getAllowUpdateFields()
    {
        return $this->allow_update_fields;
    }

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @param Validator $validator
     * @throws
     */
    public function failedValidation(Validator $validator)
    {
        foreach ($validator->failed() as $field => $items) {
            foreach ($items as $rule => $value) {

                // matchFieldRuleHandle callback
                $method = sprintf('match%s%sHandle', Str::studly($field), $rule);
                if (method_exists($this, $method)) {
                    $this->$method();
                }

                // matchRuleHandle callback
                $method = sprintf('match%sHandle', Str::studly($rule));
                if (method_exists($this, $method)) {
                    $this->$method($field, $this->get($field), $validator->errors()->first($field));
                }

                $this->failedItemCallback($field, strtolower($rule), $value);
            }
        }

        $this->failedCallback($validator, request());

        throw new ValidationException($validator->errors()->first());
    }

    /**
     * @throws
     */
    final public function passedValidation()
    {
        $this->getValidatorInstance()->validated();

        $this->passedCallback();
    }

    /**
     * 必要签名
     * @return array
     */
    final public function rules(): array
    {
        $params = [

        ];

        return array_merge($params, $this->extendRules());
    }

    final public function messages(): array
    {
        $params = [
        ];

        return array_merge($params, (array) $this->extendMessages());
    }

    /**
     * @return array
     * @throws
     */
    protected function extendMessages(): array
    {
        return [];
    }

    /**
     * @return array
     * @throws
     */
    protected function extendRules(): array
    {
        return [];
    }

    /**
     * @throws
     */
    protected function passedCallback()
    {
    }

    protected function failedCallback(Validator $validator, Request $request)
    {
    }

    /**
     * @param string $field
     * @param string $rule
     * @param  $value
     * @throws
     */
    final protected function failedItemCallback(string $field, string $rule, $value)
    {
        $this->failedItemProcessor($field, $rule, $value);
    }

    /**
     * @param string $field
     * @param string $rule
     * @param mixed $value
     * @throws
     */
    protected function failedItemProcessor(string $field, string $rule, $value)
    {
    }
}
