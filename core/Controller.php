<?php
namespace Rivulet;

use Rivulet\Http\Request;
use Rivulet\Http\Response;

abstract class Controller
{
    protected Request $request;
    protected Response $response;

    public function __construct()
    {
        $this->request  = new Request();
        $this->response = new Response();
    }

    public function validate(array $rules, array $messages = []): array
    {
        $validator = new Validation\Validator($this->request->all(), $rules, $messages);

        if ($validator->fails()) {
            abort(422, json_encode(['errors' => $validator->errors()]));
        }

        return $validator->validated();
    }

    public function authorize(string $ability, $model = null): bool
    {
        return true;
    }
}
