<?php
declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;

trait TestValidations 
{

    protected abstract function model();
    protected abstract function routesStore();
    protected abstract function routesUpdate();
    
    protected function assertInvalidationInStoreAction(
        array $data,
        string $rule,
        array $ruleParams = []
    ){
        $response = $this->json('POST', $this->routesStore(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
    }

    protected function assertInvalidationInUpdateAction(
        array $data,
        string $rule,
        array $ruleParams = []
    ){
        $response = $this->json('PUT', $this->routesUpdate(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
    }

    protected function assertInvalidationFields(
        TestResponse $response,
        array $fields,
        string $rule,
        array $ruleParams = []
    ){
        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields);
        
        foreach($fields as $field){
            $fieldName = str_replace('_', ' ', $field);
            //$response->assertJsonFragment([
            //    \Lang::trans("validation.{$rule}", ['attribute' => $fieldName] + $ruleParams)
            //]); 
        }    
    }
}
?>