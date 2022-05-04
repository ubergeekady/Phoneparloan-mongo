<?php
namespace App\Http\Requests;
// To validate the content of the JSON body instead of query params, you need to override the getValidatorInstance() method to create the validator instance with $this->json()->all() instead for $this->all()
// This way you can keep your Requests as is and just extend this class instead when you need to validate jsonbody instead of query params
use Illuminate\Http\Request;

abstract class JsonBodyRequest extends Request {
    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $factory = $this->container->make('Illuminate\Validation\Factory');
        if (method_exists($this, 'validator'))
        {
            return $this->container->call([$this, 'validator'], compact('factory'));
        }
        return $factory->make(
            $this->json()->all(), $this->container->call([$this, 'rules']), $this->messages(), $this->attributes()
        );
    }
}