<?php

namespace Framework\Validator;

class ValidationError
{
    private $key;
    private $rule;
    private $attributes;
    private $messages = [
        'required' => 'Le champ %s est requis',
        'slug' => 'Le champ %s est requis',
        'minLength' => 'Le champ %s doit contenir plus de %d caractères',
        'maxLength' => 'Le champ %s doit contenir moins de %d caractères',
        'betweenLength' => 'Le champ %s doit être compris entre %d et %d caractères',
        'datetime' => 'Le champ %s doit être une date valide(%s)',
        'empty' => 'Le champ %s n\'est pas un slug valide'
    ];

    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }
    public function __toString()
    {
        $params = array_merge([$this->messages[$this->rule], $this->key], $this->attributes);
        return (string) call_user_func_array('sprintf', $params);
    }
}
