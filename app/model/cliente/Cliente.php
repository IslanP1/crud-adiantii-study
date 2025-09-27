<?php
use Adianti\Database\TRecord;

class Cliente extends TRecord
{
    const TABLENAME = 'cliente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}

    // colocar senha de maneira criptografada
    public function set_senha($senha)
    {
        $this->data['senha'] = password_hash($senha, PASSWORD_DEFAULT);
    }

    // verificar senha no login
    public function check_senha($senha_informada)
    {
        return password_verify($senha_informada, $this->data['senha']);
    }
}